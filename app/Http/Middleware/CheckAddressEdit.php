<?php

namespace App\Http\Middleware;

use Closure;

class CheckAddressEdit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(empty($request->province_id) || !is_numeric($request->province_id)) {
            returnJsonMsg('0', '缺少必要参数', '');
        }
        if(empty($request->city_id) || !is_numeric($request->city_id)) {
            returnJsonMsg('0', '缺少必要参数', '');
        }
        if(empty($request->area_id) || !is_numeric($request->area_id)) {
            returnJsonMsg('0', '缺少必要参数', '');
        }
        if(empty($request->name)) {
            returnJsonMsg('0', '缺少联系用户', '');
        }
        if(empty($request->phone)) {
            returnJsonMsg('0', '缺少收货用户联系方式', '');
        }
        $rules = '/^1[3-9]\d{9}$/';
        if(!preg_match($rules,$request->phone)) {
            returnJsonMsg('0', '手机号格式不正确', '');
        }

        return $next($request);
    }
}
