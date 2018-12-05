<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Good;
use App\Models\Comment;

class GoodsDetailController extends Controller
{
    /**
     * 显示用户所选的商品详情
     *
     * @return \Illuminate\Http\Response
     */
    public function goodsDetail(Request $request, $id)
    {
        // $id = $request->has('id') ? $request->get('id') : 0;

        $goods = Good::find($id)->first();
        // 获取商品 sku
        $goods_sku = $goods->sku->first();

        return $goods;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function goodsComment(Request $request, $id)
    {
        $uid = 4;
        // return $uid;
        $comments = Comment::where('g_id', $id)->where('c_id', $uid)->with('consumer')->get();
        return $comments;
    }
}
