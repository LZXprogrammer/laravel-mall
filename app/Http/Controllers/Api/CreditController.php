<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Article;
use App\Models\CreditCard;
use App\Models\Comment;

class CreditController extends Controller
{
    /**
     * 信用卡列表轮播图 type = 2 
     *
     * @return \Illuminate\Http\Response
     */
    public function creditBanner()
    {
        $credit_banner = Article::select(['picture', 'url'])->whereType(2)->get();

        return $credit_banner;
    }

    /**
     * 信用卡列表
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function creditList(Request $request)
    {
        $type = $request->has('type') ? $request->get('type') : 'hot';

        switch ($type) {
            case 'hot':
                $hot_credits = CreditCard::where('is_hot', 1)->with('credit_type')->get();
                return $hot_credits;
                break;
            case 'new':
                $new_credits = CreditCard::where('is_new', 1)->with('credit_type')->get();
                return $new_credits;
                break;

            default:
                # code...
                break;
        }
    }

    /**
     * 信用卡详情
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function creditDetail($id)
    {
        $credir_detail = CreditCard::whereId($id)->first();
        
        return $credir_detail;
    }

    /**
     * 信用卡详情下的评论
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function creditComment(Request $request, $id)
    {
        // $uid = $request->session()->get('uid');
        $uid = 4;
        // return $uid;
        $comments = Comment::where('credit_id', $id)->where('c_id', $uid)->with('consumer')->get();
        return $comments;
    }

}
