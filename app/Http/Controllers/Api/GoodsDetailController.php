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
    public function goodsDetail(Request $request)
    {
        $goods = Good::where('id', $request->id)->first();

        if(!$goods){
            return ['code' => 0, 'message' => '请求商品不存在', 'data' => ''];
        }

        // 获取商品 sku
        $goods_sku = $goods->sku()->select(['id', 'trad_channel', 'extra'])->get();
        $goods->sku = $goods_sku;

        return ['code' => 1, 'message' => '请求商品详情成功', 'data' => $goods];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Int  $id 商品id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function goodsComment(Request $request, $id)
    {
        $uid = $request->session()->get('uid');

        $comments = Comment::where('g_id', $id)->where('c_id', $uid)->with('consumer')->get();

        return ['code' => 1, 'message' => '请求商品评论成功', 'data' => $comments];
    }
}
