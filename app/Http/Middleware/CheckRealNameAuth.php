<?php

namespace App\Http\Middleware;

use Closure;

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
        if(empty($request->realName)) {
            returnJsonMsg('0', '用户姓名不能为空', '');
        }
        if(empty($request->idCard)) {
            returnJsonMsg('0', '用户身份证号不能为空', '');
        }
        return $next($request);
    }
}
