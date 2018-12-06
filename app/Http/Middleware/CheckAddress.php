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
            return response()->json(['code' => 0, 'message' => '缺少必要参数', 'data' => '']);
        }

        return $next($request);
    }
}
