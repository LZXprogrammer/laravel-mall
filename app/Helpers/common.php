<?php

function returnJsonMsg($code, $message, $data) {
    if(!is_numeric($code)){
        return '';
    }
    $result=array(
        'code'=>$code,
        'message'=>$message,
        'data'=>$data
    );
    // arrayRecursive($result, 'urlencode', true);
    // return urldecode(json_encode($result));
    return json_encode($result);
}

function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
{
    static $recursive_counter = 0;
    if (++$recursive_counter > 1000)
    {
        die('possible deep recursion attack');
    }
    foreach ($array as $key => $value)
    {
        if (is_array($value))
        {
            arrayRecursive($array[$key], $function, $apply_to_keys_also);
        }
        else
        {
            $array[$key] = $function($value);
        }
        if ($apply_to_keys_also && is_string($key))
        {
            $new_key = $function($key);
            if ($new_key != $key)
            {
                $array[$new_key] = $array[$key];
                unset($array[$key]);
            }
        }
    }
    $recursive_counter--;
}

//生成随机数
function randFloat($length = 6){
    return rand(pow(10,($length-1)), pow(10,$length)-1);
}

function getPromote( $length = 8)
{
    return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'),0,$length);
}