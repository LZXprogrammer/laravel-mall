<?php

namespace App\Http\Middleware;

use Closure;

class CheckPassword
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
        if(empty($request->password)) {
            return response()->json(['code' => '0', 'message' => '用户密码不能为空', 'data' => '']);
        }

        return $next($request);
    }
}
