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

        return ['code' => 1, 'message' => '请求信用卡banner图成功', 'data' => $credit_banner];
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
                $data_lists = CreditCard::where('is_hot', 1)
                                        ->select(['id', 'bank_name', 'name', 'picture', 'apply_num', 'blurb'])
                                        ->get();
                break;
            case 'new':
                $data_lists = CreditCard::where('is_new', 1)
                                        ->select(['id', 'bank_name', 'name', 'picture', 'apply_num', 'blurb'])
                                        ->get();
                break;

            default:
                # code...
                break;
        }
        return ['code' => 1, 'message' => '请求信用卡列表成功', 'data' => $data_lists];
    }

    /**
     * 信用卡详情
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function creditDetail(Request $request)
    {
        $card_id = $request->has('id') ? $request->id : 0;

        $credir_detail = CreditCard::where('id', $card_id)
                                    ->select(['id', 'bank_name', 'type', 'name', 'picture', 'apply_num', 'blurb', 'content', 
                                            'sort', 'is_hot', 'is_new'])->first();
        $credit_type = $credir_detail->credit_type()->select(['id', 'name'])->first();

        $credir_detail->type_id = $credit_type->id;
        $credir_detail->type = $credit_type->name;

        if(!$credir_detail){
            return ['code' => 0, 'message' => '请求信用卡不存在', 'data' => ''];
        }
        
        return ['code' => 1, 'message' => '请求信用卡详情成功', 'data' => $credir_detail];
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
        $uid = $request->session()->get('uid');

        $comments = Comment::where('credit_id', $id)->where('c_id', $uid)->with('consumer')->get();

        return ['code' => 1, 'message' => '请求信用卡评论成功', 'data' => $comments];
    }

}
