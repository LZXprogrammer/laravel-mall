<?php

namespace App\Http\Middleware;

use Closure;

class CheckMobile
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
        if(empty($request->mobile)) {
            return response()->json(['code' => 0, 'message' => '用户手机号不能为空', 'data' => '']);
        }

        $rules = '/^1[3-9]\d{9}$/';
        if(!preg_match($rules,$request->mobile)) {
            return response()->json(['code' => 0, 'message' => '手机号格式不正确', 'data' => '']);
        }

        return $next($request);
    }
}
