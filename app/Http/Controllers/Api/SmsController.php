<?php

namespace App\Http\Controllers\Api;

use App\Models\Consumer;
use App\Models\Message;
use App\Models\MessageTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Notifications\SmsVerificationNotification;

class SmsController extends Controller
{
    protected $_sms;

    public function __construct(SmsVerificationNotification $smsVerificationNotification){
        $this->_sms = $smsVerificationNotification;
    }
    /**
     * 发送短信
     *
     * @return array()
     */
    public function index(Request $request)
    {
        $mobile = $request->post('mobile');
        $type = $request->post('type');

        $send = Message::where('mobile' , $mobile)->where('message_template_id', $type)->where('is_use', '0')
                       ->where('overdue_time', '>=', time())->first();
        //开启事务
        DB::beginTransaction();

        if(!empty($send)) {
            $send_time = $send->send_time+60;
            //避免用户太频繁发送短信
            if($send_time >= time()) {
                returnJsonMsg('0', '用户发送短信较频繁，请稍等60s后再发送', '');
            }
            $res = Message::where('id' , $send->id)->update(['overdue_time' => time()+300]);
            if(!$res) {
                DB::rollback();
                returnJsonMsg('0', '用户发送短信失败', '');
            }
            $message = $send->message;
        }else{
            $content = MessageTemplate::where('id', $type)->value('content');

            if(empty($content)) {
                returnJsonMsg('0', '用户非法操作', '');
            }
            //获取发送短信
            $info = $this->editText($mobile, $content);

            $inset = [
                'mobile' => $mobile,
                'message_template_id' => $type,
                'code' => $info['code'],
                'message' => $info['message'],
                'send_time' =>time(),
                'overdue_time' =>time()+300,
                'is_use' => '0',
                'type' => '1'
            ];
            $res = Message::create($inset);

            $message = $info['message'];
        }
        if(!$res) {
            DB::rollback();
            returnJsonMsg('0', '用户发送短信失败', '');
        }

        if(Config::get('systems.environment') == 'production') {
            //调取短信通道
            $waugh = $this->_sms->waugh($mobile, $message);
            if ($waugh != '1') {
                DB::rollBack();
                returnJsonMsg('0', $waugh, '');
            }
        }

        //提交数据
        DB::commit();
        returnJsonMsg('1', '发送成功', '');
    }

    //拼装发送短信
    private function editText($mobile, $content) {
        $code    = '';
        $message = '';
        if(strpos($content,'{code}') ) {
            $code = randFloat();
            $message = str_replace('{code}', $code, $content);
        }
        if(strpos($content,'{mobile}') ) {
            $message = str_replace('{code}', $mobile, $content);
        }
        if(strpos($content,'{real_name}') ) {
            $real_name = Consumer::where('mobile', $mobile)->value('real_name');
            $message = str_replace('{code}', $real_name, $content);
        }
        $message = $message.' 【POS支付商城】';
        $info['message'] = (string)$message;
        $info['code'] = (string)$code;
        return $info;
    }
}
