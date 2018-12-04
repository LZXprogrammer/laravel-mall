<?php

namespace App\Http\Middleware;

use Closure;

class CheckCode
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
        if(empty($request->code)) {
            returnJsonMsg('0', '用户手机验证码不能为空', '');
        }

        return $next($request);
    }
}
