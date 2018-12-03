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
        $rules = '/^1[3-9]\d{9}$/';
        if(!preg_match($rules,$request->mobile)) {
            returnJsonMsg('0', '手机号格式不正确', '');
        }

        return $next($request);
    }
}
