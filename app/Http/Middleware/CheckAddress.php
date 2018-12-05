<?php

namespace App\Http\Middleware;

use Closure;

class CheckAddress
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
        if(empty($request->id) || !is_numeric($request->id)) {
            returnJsonMsg('0', '缺少必要参数', '');
        }

        return $next($request);
    }
}
