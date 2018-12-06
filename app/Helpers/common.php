<?php

//生成随机数
function randFloat($length = 6){
    return rand(pow(10,($length-1)), pow(10,$length)-1);
}

//生成推广码
function getPromote( $length = 8)
{
    return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'),0,$length);
}
