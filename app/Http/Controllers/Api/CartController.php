<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GoodSku;
use App\Models\Cart;

class CartController extends Controller
{
    /**
     * 添加购物车
     *
     * @param Int $g_sku_id 商品sku id
     * @return \Illuminate\Http\Response
     */
    public function cartAdd(Request $request)
    {   
        $uid = $request->session()->get('uid');

        $g_sku_id  = $request->has('g_sku_id') ? $request->input('g_sku_id') : 0;

        $goods_sku = GoodSku::where('id', $g_sku_id)->select(['id', 'g_id', 'trad_channel', 'extra'])->first();

        if(!$goods_sku){
            return ['code' => 0, 'message' => '没有该商品', 'data' => 'g_sku_id: '.$request->input('g_sku_id')];
        }

        $goods = $goods_sku->good()->select(['name', 'category', 'price', 'show_pic'])->first();

        $cart['c_id']         = $uid;
        $cart['g_sku_id']     = $goods_sku->id;
        $cart['g_id']         = $goods_sku->g_id;
        $cart['trad_channel'] = $goods_sku->trad_channel;
        $cart['extra']        = $goods_sku->extra;
        $cart['name']         = $goods->name;
        $cart['price']        = $goods->price;
        $cart['category']     = $goods->category;
        $cart['show_pic']     = $goods->show_pic;
        $cart['amount']       = 1;
        $cart['create_time']  = time();

        $data = Cart::create($cart);

        if($data){
            return ['code' => 1, 'message' => '添加购物车成功', 'data' => ''];
        }
    }

    /**
     * 当前用户的购物车列表
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cartList(Request $request)
    {   
        $uid = $request->session()->get('uid');

        $cart_lists = Cart::where('c_id', $uid)->get();

        // get方法返回的是一个集合,用empty判断其值是不为空的,所以要先转成数组
        if(empty($cart_lists->toArray())){
            
            return ['code' => 1, 'message' => '购物车空空如也,当前用户要么正在吃土,要么就是穷逼', 'data' => ''];
        }

        return ['code' => 1, 'message' => '请求购物车列表成功', 'data' => $cart_lists];
    }
}
