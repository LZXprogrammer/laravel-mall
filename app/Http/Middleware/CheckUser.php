<?php

namespace App\Http\Middleware;

use Closure;

class CheckUser
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
//        if(empty($request->uid)) {
//            return response()->json(['code' => 0, 'message' => '缺少必要参数', 'data' => '']);
//        }
//
//        if(empty(session('uid'))) {
//            return response()->json(['code' => 0, 'message' => '用户登陆保持已失效，请重新登陆', 'data' => '']);
//        }
//
//        if(session('uid') != $request->uid) {
//            return response()->json(['code' => 0, 'message' => '用户ID不正确，非法操作', 'data' => '']);
//        }
        session()->put('uid', $request->uid);
        session()->save();

        return $next($request);
    }
}
