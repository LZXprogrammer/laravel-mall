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

/**
 * 验证身份证号
 */
function isChinaIDCard($id)
{
    if(!$this->get_shenfen($id)){
        return false;
    }
    $len = strlen($id);
    if($len == 18){
        if (!$this->isChinaIDCardDate(substr($id,6,4), substr($id,10,2), substr($id,12,2))){
            return false;
        }
        $code = $this->getValidateCode($id);
        if (strtoupper($code) == substr($id,17,1)){
            return true;
        }
        return false;
    }
    else if($len == 15)
    {
        if(!$this->isChinaIDCardDate('19'.substr($id,6,2),substr($id,8,2),substr($id,10,2))){
            return false;
        }
        if(!is_numeric($id)){
            return false;
        }
        return true;
    }
    return false;
}

/**
 * 根据身份证号，自动返回对应的性别
 */
function getChinaIDCardSex($cid)
{
    $sexint = (int)substr($cid,16,1);
    return $sexint % 2 === 0 ? '女' : '男';
}

/**
 * 根据身份证号，自动返回对应的省、自治区、直辖市代
 */
function get_shenfen($id){
    $index = substr($id,0,2);
    $area = array(
        11 => "北京",  12 => "天津", 13 => "河北",   14 => "山西", 15 => "内蒙古", 21 => "辽宁",
        22 => "吉林",  23 => "黑龙江", 31 => "上海",  32 => "江苏",  33 => "浙江", 34 => "安徽",
        35 => "福建",  36 => "江西", 37 => "山东", 41 => "河南", 42 => "湖北",  43 => "湖南",
        44 => "广东", 45 => "广西",  46 => "海南", 50 => "重庆", 51 => "四川", 52 => "贵州",
        53 => "云南", 54 => "西藏", 61 => "陕西", 62 => "甘肃", 63 => "青海", 64 => "宁夏",
        65 => "新疆", 71 => "台湾", 81 => "香港", 82 => "澳门", 91 => "国外"
    );
    return $area[$index];
}

/**
 * 验证出生日期
 */
function isChinaIDCardDate($iY, $iM, $iD)
{
    $iDate =  $iY . '-' . $iM . '-' . $iD;
    $rPattern = '/^(([0-9]{2})|(19[0-9]{2})|(20[0-9]{2}))-((0[1-9]{1})|(1[012]{1}))-((0[1-9]{1})|(1[0-9]{1})|(2[0-9]{1})|3[01]{1})$/';
    if(preg_match($rPattern, $iDate, $arr)){
        $this->birthday = $iDate;
        return true;
    }
    return false;
}

/**
 * 根据身份证号前17位, 算出识别码
 */
function getValidateCode($id)
{
    $id17 = substr($id,0,17);
    $sum = 0;
    $len = strlen($id17);
    for ($i=0; $i<$len; $i++){
        $sum += $id17[$i] * $this->aWeight[$i];
    }
    $mode = $sum % 11;
    return $this->aValidate[$mode];
}