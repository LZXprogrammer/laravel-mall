<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;

class BankVerificationNotification extends Notification
{
    public function CheckBank($bank_card)
    {
        $bank_url = Config::get("systems.checkBank");
        $data['_input_charset'] = 'utf-8';
        $data['cardNo'] = $bank_card;
        $data['cardBinCheck'] = 'true';
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
        curl_setopt($ch, CURLOPT_URL,$bank_url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);
        if($output === FALSE ){
            return ['code'=>'0' ,'msg'=>'发送失败'];
        }
        $output = json_decode($output);
        if(!$output->validated) {
            return ['code'=>'0' ,'msg'=>$output->messages['0']->errorCodes];
        }
        // 4. 释放curl句柄
        curl_close($ch);
        return ['code'=>'1' ,'msg'=>$output->bank];
    }
}
