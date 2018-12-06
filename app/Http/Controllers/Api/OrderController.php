<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\HarvestAddress;
use App\Models\Good;
use App\Models\GoodSku;
use App\Models\Order;
use App\Models\OrderItem;

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
        $province = $address->province()->first();
        $city = $address->city()->first();
        $area = $address->area()->first();

        $address->province = $province->toArray()['name'];
        $address->city = $city->toArray()['name'];
        $address->area = $area->toArray()['name'];

        return ['code' => '1', 'message' => '获取默认收获地址成功', 'data' => $address];
    }

    /**
     * 确认订单 - 订单商品信息
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function orderGoods(Request $request)
    {
        $g_sku_id = $request->has('g_sku_id') ? $request->input('g_sku_id') : 0;

        $goods_sku = GoodSku::where('id', $g_sku_id)->select(['id','g_id','trad_channel','extra'])->first();
        $goods = $goods_sku->good()->select(['name', 'category', 'courier_fees'])->first();

        $info = array_merge($goods_sku->toArray(), $goods->toArray());

        return ['code' => '1', 'message' => '获取默认收获地址成功', 'data' => $info];
    }

    /**
     * 确认订单 - 提交订单
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function submitOrder(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
