<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GoodSku;
use App\Models\Cart;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function cartAdd(Request $request)
    {
        // $uid = $request->session()->get('uid');
        // return 11;
        $uid = 4;
        $g_sku_id  = $request->has('g_sku_id') ? $request->input('g_sku_id') : 0;

        if($g_sku_id){

            $goods_sku = GoodSku::where('id', $g_sku_id)->select(['id', 'g_id', 'trad_channel'])->first();
            $goods = $goods_sku->good()->select(['name', 'category', 'price'])->first();

            $cart['c_id'] = $uid;
            $cart['g_sku_id'] = $goods_sku->id;
            $cart['amount'] = 1;
            $cart['create_time'] = time();

            $data = Cart::create($cart);

            if($data){
                return returnJsonMsg(1, '添加成功');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cartList()
    {
        $cart_lists = Cart::get();

        return $cart_lists;
    }
}
