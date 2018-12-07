<?php

namespace App\Http\Middleware;

use Closure;

class CheckLevel
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
        if(empty($request->level) || !is_numeric($request->level)) {
            return response()->json(['code' => 0, 'message' => '缺少必要参数或者参数违规', 'data' => '']);
        }
        if(!in_array($request->level, ['1', '2', '3'])) {
            return response()->json(['code' => 0, 'message' => '用户会员级别参数超出限制', 'data' => '']);
        }
        return $next($request);
    }
}
