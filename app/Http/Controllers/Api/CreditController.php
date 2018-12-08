<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Article;
use App\Models\CreditCard;
use App\Models\Comment;
use App\Models\CommentReply;
use App\Models\Consumer;

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
     * 信用卡详情下的用户评论列表
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function creditComment(Request $request)
    {   
        $card_id = $request->has('id') ? $request->id : 0;

        $comments = Comment::where('credit_id', $card_id)
                            ->with(['consumer', 'comment_replies'])
                            ->get();

        // return $comments;
    
        if(empty($comments->toArray())){
            return ['code' => 0, 'message' => '该信用卡下没有评论', 'data' => ''];
        }
        $infos = [];

        foreach ($comments as $key => $comment) {
            // var_dump($comment);
            $infos[$key]['comment_id'] = $comment->id;
            $infos[$key]['credit_id'] = $comment->credit_id;
            $infos[$key]['content'] = $comment->content;
            $infos[$key]['status'] = $comment->status;
            $infos[$key]['create_time'] = date('Y-m-d H:i', $comment->create_time);

            // foreach ($comment as $kk => $consumer) {
            //     var_dump($consumer);
            // }
            $infos[$key]['consumer']['c_id'] = $comment->consumer->id;
            $infos[$key]['consumer']['nick_name'] = $comment->consumer->nick_name ?? $comment->consumer->mobile;
            $infos[$key]['consumer']['avatar'] = $comment->consumer->avatar;
            $infos[$key]['consumer']['real_name'] = $comment->consumer->real_name;
            
            // var_dump($comment['comment_replies']);

            foreach ($comment->comment_replies as $kk => $comment_reply) {
                // var_dump($comment_reply);
                if($comment_reply->reply_type == 1){
                    $infos[$key]['comment_reply'][$kk]['reply_id'] = $comment_reply->id;
                    $infos[$key]['comment_reply'][$kk]['to_cid'] = $comment_reply->to_cid;
                    $infos[$key]['comment_reply'][$kk]['from_cid'] = $comment_reply->from_cid;
                    $infos[$key]['comment_reply'][$kk]['from_nickname'] = $comment_reply->from_nickname;
                    $infos[$key]['comment_reply'][$kk]['from_avatar'] = $comment_reply->from_avatar;
                    $infos[$key]['comment_reply'][$kk]['reply_type'] = $comment_reply->reply_type;
                    $infos[$key]['comment_reply'][$kk]['content'] = $comment_reply->content;
                    $infos[$key]['comment_reply'][$kk]['reply_time'] = date('Y-m-d H:i', $comment->create_time);

                }else{
                    // foreach ($variable as $key => $value) {
                    //     # code...
                    // }
                }
                

            }
            
        }
        // die;
        return $infos;


        // $replies = 
        // die;

        // 拼接返回参数
//         $comments_num = count($comments);
//         foreach ($comments as $key => $value) {

//             $replies = CommentReply::where('comment_id', $value->id)->get();
// // var_dump($replies);
//             foreach ($replies as $k => $reply) {
//                 // var_dump($reply);
//                 $replies[$k]['id'] =  $reply->id;
//                 $replies[$k]['comment_id'] =  $reply->comment_id;
//                 $replies[$k]['content'] =  $reply->content;
//                 $replies[$k]['from_cid'] =  $reply->from_cid;
//                 $replies[$k]['from_avatar'] =  $reply->from_avatar;
//                 $replies[$k]['from_nickname'] =  $reply->from_nickname;
//             }
//             $infos[] = Consumer::where('id', $value->c_id)->select(['nick_name', 'avatar'])->first();
//             $infos[$key]['id'] = $value->id;
//             $infos[$key]['c_id'] = $value->c_id;
//             $infos[$key]['content'] = $value->content;
//             $infos[$key]['create_time'] = date('Y-m-d H:i', $value->create_time);
//             $infos[$key]['replies'] = $replies;
//         }
// // die;
//         return $infos;

        // return ['code' => 1, 'message' => '请求信用卡评论成功', 'comments_num' => $comments_num, 'data' => $infos];

        
    
    
    
    
    }

}
