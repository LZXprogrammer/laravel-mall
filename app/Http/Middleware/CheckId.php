<?php

namespace App\Http\Middleware;

use Closure;

class CheckId
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
        if(empty($request->id)) {
            return response()->json(['code' => 0, 'message' => '必要参数不能为空', 'data' => '']);
        }
        if(!is_numeric($request->id)) {
            return response()->json(['code' => 0, 'message' => '必要参数类别不符合规则', 'data' => '']);
        }
        return $next($request);
    }
}
