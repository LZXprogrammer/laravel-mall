<?php

namespace App\Http\Middleware;

use Closure;

class CheckNewPassword
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
        if(empty($request->new_pwd)) {
            returnJsonMsg('0', '用户新密码不能为空', '');
        }

        return $next($request);
    }
}
