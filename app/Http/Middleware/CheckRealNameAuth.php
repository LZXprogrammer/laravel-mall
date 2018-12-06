<?php

namespace App\Http\Middleware;

use Closure;
use Medz\IdentityCard\China\Identity;

class CheckRealNameAuth
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
        if(empty($request->real_name)) {
            return response()->json(['code' => '0', 'message' => '用户姓名不能为空', 'data' => '']);
        }

        if(empty($request->idCard)) {
            return response()->json(['code' => '0', 'message' => '用户身份证号不能为空', 'data' => '']);
        }

        $check = new Identity($request->idCard);
        if(!$check->legal()) {
            return response()->json(['code' => '0', 'message' => '用户身份证号不正确', 'data' => '']);
        }
        return $next($request);
    }
}
