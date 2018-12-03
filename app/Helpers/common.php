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
    arrayRecursive($result, 'urlencode', true);
    echo urldecode(json_encode($result));
    exit;
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