<?php

namespace App\Http\Middleware;

use Closure;

class CheckQuery
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
        if(!empty($request->begin_time) && !is_numeric($request->begin_time)) {
            return response()->json(['code' => 0, 'message' => '开始时间不符合规则', 'data' => '']);
        }
        if(!empty($request->end_time) && !is_numeric($request->end_time)) {
            return response()->json(['code' => 0, 'message' => '结束时间不符合规则', 'data' => '']);
        }
        if(!empty($request->sku_id) && !is_numeric($request->sku_id)) {
            return response()->json(['code' => 0, 'message' => '通道ID不符合规则', 'data' => '']);
        }
        if(!empty($request->level) && !is_numeric($request->level) && !in_array($request->level, ['1', '2', '3'])) {
            return response()->json(['code' => 0, 'message' => '会员等级不符合规则', 'data' => '']);
        }
        if(!empty($request->mobile) || !preg_match('/^1[3-9]\d{9}$/',$request->mobile)) {
            return response()->json(['code' => 0, 'message' => '用户手机号不能为空', 'data' => '']);
        }

        return $next($request);
    }
}
