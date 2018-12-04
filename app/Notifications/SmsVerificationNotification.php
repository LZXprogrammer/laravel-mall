<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;

class SmsVerificationNotification extends Notification
{
    /**
     * 发送短信
     *
     * @return void
     */
    public function waugh($mobile, $message){
        //获取短信相关配置
        $waugh = Config::get("sms.waugh");
        $data['userid'] = (string)$waugh['userid'];
        $data['account'] = (string)$waugh['account'];
        $data['password'] = (string)$waugh['password'];
        $data['mobile'] = (string)$mobile;
        $data['content'] = $message;
        $data['sendtime'] = '';
        $data['json'] = '1';
        $ch = curl_init();
        $o='';
        foreach ($data as $k=>$v)
        {
            $o.="$k=".urlencode($v).'&';
        }
        $post_data=substr($o,0,-1);
        // 2. 设置选项，包括URL
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL,$waugh['url']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);
        if($output === FALSE ){
            return '发送失败';
        }
        $output = json_decode($output);
        if($output->code == "Faild") {
            return $output->msg;
        }
        // 4. 释放curl句柄
        curl_close($ch);
        return '1';
    }
}
