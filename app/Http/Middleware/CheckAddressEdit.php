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
            return response()->json(['code' => 0, 'message' => '缺少必要参数', 'data' => '']);
        }
        if(empty($request->city_id) || !is_numeric($request->city_id)) {
            return response()->json(['code' => 0, 'message' => '缺少必要参数', 'data' => '']);
        }
        if(empty($request->area_id) || !is_numeric($request->area_id)) {
            return response()->json(['code' => 0, 'message' => '缺少必要参数', 'data' => '']);
        }
        if(empty($request->name)) {
            return response()->json(['code' => 0, 'message' => '缺少联系用户', 'data' => '']);
        }
        if(empty($request->phone)) {
            return response()->json(['code' => 0, 'message' => '缺少收货用户联系方式', 'data' => '']);
        }
        $rules = '/^1[3-9]\d{9}$/';
        if(!preg_match($rules,$request->phone)) {
            return response()->json(['code' => 0, 'message' => '手机号格式不正确', 'data' => '']);
        }

        return $next($request);
    }
}
