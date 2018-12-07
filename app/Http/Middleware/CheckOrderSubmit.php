<?php

namespace App\Http\Middleware;

use Closure;

class CheckOrderSubmit
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
        if(empty($request->address_id)) {
            return response()->json(['code' => 0, 'message' => '缺少收货地址id', 'data' => '']);
        }

        if(empty($request->g_sku_id)) {
            return response()->json(['code' => 0, 'message' => '缺少商品sku id', 'data' => '']);
        }

        // if(empty($request->remarks)) {
        //     return response()->json(['code' => 0, 'message' => '', 'data' => '']);
        // }

        return $next($request);
    }
}
