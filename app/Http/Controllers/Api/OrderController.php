<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\HarvestAddress;
use App\Models\Good;
use App\Models\GoodSku;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * 确认订单 - 默认收货地址
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function orderAddress(Request $request)
    {
        $uid = $request->session()->get('uid');

        $address = HarvestAddress::where('c_id', $uid)->where('is_default', 1)->first();
        if(!$address){

            return ['code' => 0, 'message' => '当前用户没有收货地址', 'data' => ''];
        }else{

            $province = $address->province()->first();
            $city = $address->city()->first();
            $area = $address->area()->first();

            $address->province = $province->toArray()['name'];
            $address->city = $city->toArray()['name'];
            $address->area = $area->toArray()['name'];
        }      

        return ['code' => 1, 'message' => '获取默认收获地址成功', 'data' => $address];
    }

    /**
     * 确认订单 - 立即购买订单商品信息(单个商品)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function orderGoodsBuyNow(Request $request)
    {
        $g_sku_id = $request->has('g_sku_id') ? $request->input('g_sku_id') : 0;

        $goods_sku = GoodSku::where('id', $g_sku_id)->select(['g_id','trad_channel','extra'])->first();

        if(!$goods_sku){
            return ['code' => 0, 'message' => '没有该商品', 'data' => $g_sku_id];
        }

        $goods = $goods_sku->good()->select(['name', 'category', 'courier_fees', 'show_pic', 'price'])->first();
        $goods_sku->g_sku_id = $g_sku_id;
        $info = array_merge($goods_sku->toArray(), $goods->toArray());

        return ['code' => 1, 'message' => '获取商品信息成功', 'data' => $info];
    }

    /**
     * 确认订单 - 购物车结算时订单商品信息(单个或多个商品)
     *
     * @param  Json $request->cart_infos = [{"cart_id":1,"amount":2},{"cart_id":2,"amount":2}]
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function orderGoodsBuyCart(Request $request)
    {   
        $cart_infos = json_decode($request->cart_infos, true);

        foreach($cart_infos as $key => $value)
        {
            $infos[] = Cart::where('id', $value['cart_id'])
                            ->select(['g_sku_id', 'g_id', 'name', 'trad_channel', 'show_pic', 'category', 'price', 'extra'])
                            ->first();
            $infos[$key]['amount'] = $value['amount'];
            $infos[$key]['cart_id'] = $value['cart_id'];
        }

        return ['code' => 1, 'message' => '获取商品信息成功', 'data' => $infos];
    }

    /**
     * 确认订单 - 提交订单
     *
     * @param  Json $request->g_sku = [{"g_id":1,"g_sku_id":3,"amount":1},{"g_id":2,"g_sku_id":4,"amount":2}]
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function orderSubmit(Request $request)
    {
        $remarks = $request->has('remarks') ? $request->input('remarks') : '';

        $g_sku_infos = json_decode($request->g_sku_infos, true);

        // 该笔订单总金额 
        $total_amount = 0;
        foreach ($g_sku_infos as $key => $value) {

            $amount = $value['amount'] ? $value['amount'] : 1;
            $goods = GoodSku::where('id', $value['g_sku_id'])->where('g_id', $value['g_id'])->with('good')->first();
            if(!$goods){

                return ['code' => 0, 'message' => '请求商品不存在', 'data' => $g_sku_infos];
            }
            $total_amount += $goods['good']['price'] * $amount;
            $g_sku_infos[$key]['price'] = $goods['good']['price'];
        }

        // 该笔订单收货地址
        $address = HarvestAddress::where('id', $request->address_id)->first();

        $address_arr['address'] = $address->full_address;
        $address_arr['phone'] = $address->phone;
        $address_arr['name'] = $address->name;

        // 整合订单需要字段
        $order = [
            'no' => 'POS'.date('Ymd').substr(microtime(true),0,10),
            'address' => json_encode($address_arr),
            'c_id' => $request->session()->get('uid'),
            'total_amount' => $total_amount,
            'remark' => $request->remark,
            'create_time' => time(),
        ];

        // 订单表入库, 并获取当前插入的 order_id
        $order_id = Order::insertGetId($order);

        // 一笔订单多个商品入库
        foreach ($g_sku_infos as $key => $value) {
            $order_items = [
                'order_id' => $order_id,
                'product_id' => $value['g_id'],
                'product_sku_id' => $value['g_sku_id'],
                'amount' => $value['amount'],
                'price' => $value['price'],
                'create_time' => time(),
            ];

            $info = OrderItem::create($order_items);
        }

        if($info){
            return ['code' => 1, 'message' => '下单成功', 'data' => ''];
        }
    }

}
