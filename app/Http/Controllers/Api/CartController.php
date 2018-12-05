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

        if($g_sku_id){

            $goods_sku = GoodSku::where('id', $g_sku_id)->select(['id', 'g_id', 'trad_channel'])->first();
            $goods = $goods_sku->good()->select(['name', 'category', 'price', 'show_pic'])->first();

            $cart['c_id']         = $uid;
            $cart['g_sku_id']     = $goods_sku->id;
            $cart['g_id']         = $goods_sku->g_id;
            $cart['trad_channel'] = $goods_sku->trad_channel;
            $cart['name']         = $goods->name;
            $cart['price']        = $goods->price;
            $cart['category']     = $goods->category;
            $cart['show_pic']     = $goods->show_pic;
            $cart['amount']       = 1;
            $cart['create_time']  = time();

            $data = Cart::create($cart);

            if($data){
                return returnJsonMsg(1, '添加成功', '');
            }
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

        return $cart_lists;
    }
}
