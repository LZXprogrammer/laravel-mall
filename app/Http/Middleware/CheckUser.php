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
        if(empty($request->uid)) {
            returnJsonMsg('0', '缺少必要参数', '');
        }

        if(empty(session('uid'))) {
            returnJsonMsg('0', '用户登陆保持已失效，请重新登陆', '');
        }

        if(session('uid') != $request->uid) {
            returnJsonMsg('0', '用户ID不正确，非法操作', '');
        }


        return $next($request);
    }
}
