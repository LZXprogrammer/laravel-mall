<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;

class realNameAuthNotification extends Notification
{

    /**
     * 身份证实名认证
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function idCard($real_name, $id_card)
    {
        $verify = Config::get("systems.verifyIdCard");
        $method = "GET";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $verify['app_code']);
        $query = "cardNo=".$id_card."&realName=".$real_name;
        $url = $verify['host'] . $verify['path'] . "?" . $query;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if (1 == strpos("$".$verify['host'], "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $output = curl_exec($curl);
        if($output === ''){
            return '请求失败';
        }

        $output = json_decode($output);

        if($output->error_code != "206501") {
            return '认证中心库中无此身份证记录';
        }
        if($output->result['isok'] == 'false') {
            return '用户输入的姓名和身份证号不匹配';
        }
        // 4. 释放curl句柄
        curl_close($curl);
        return '1';
    }
}
