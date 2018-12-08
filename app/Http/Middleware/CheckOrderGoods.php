<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\HarvestAddress;

class CheckOrderGoods
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

        // $g_sku_id = $request->has('g_sku_id') ? $request->input('g_sku_id') : 0;

        $cart_infos = $request->has('cart_infos') ? $request->input('cart_infos') : 0;

        // 商品信息参数
        // if(empty($g_sku_id)) {

        //     return response()->json(['code' => 0, 'message' => '缺少g_sku_id', 'data' => '']);
        // }

        // 购物车信息参数
        if(empty($cart_infos)){

            return response()->json(['code' => 0, 'message' => '缺少购物车参数', 'data' => '']);
        }else{

            $cart_infos = json_decode($request->cart_infos, true);
            
            // 判断 json 是否正确
            if($error = json_last_error()){
                return response()->json(['code' => 0, 'message' => '一个无效的json', 'data' => $error]);
            }

            foreach ($cart_infos as $key => $value) {
                if(!array_key_exists('cart_id', $value) || !array_key_exists('amount', $value)){
                    
                    return response()->json(['code' => 0, 'message' => '缺少 cart_id 或者 amount', 'data' => '']);
                }
            }
        }

        return $next($request);
    }
}
