<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\HarvestAddress;

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
        // 收货地址参数
        if(empty($request->address_id)) {

            return response()->json(['code' => 0, 'message' => '缺少 address_id', 'data' => '']);
        }else{

            $address = HarvestAddress::where('c_id', $request->session()->get('uid'))->where('id', $request->address_id)->first();
            if(!$address){

                return response()->json(['code' => 0, 'message' => 'address_id 不正确,非法操作', 'data' => '']);
            }
        }

        // 商品信息参数
        if(empty($request->g_sku_infos)) {

            return response()->json(['code' => 0, 'message' => '缺少商品信息', 'data' => '']);
        }else{

            $g_sku_infos = json_decode($request->g_sku_infos, true);

            // 判断 json 是否正确
            if($error = json_last_error()){
                return response()->json(['code' => 0, 'message' => '一个无效的json', 'data' => $error]);
            }

            foreach ($g_sku_infos as $key => $value) {
                if(!array_key_exists('g_id', $value) || !array_key_exists('g_sku_id', $value) || !array_key_exists('amount', $value)){
                    
                    return response()->json(['code' => 0, 'message' => '缺少 g_id 或者 g_sku_id 或者 amount', 'data' => '']);
                }
            }
        }

        return $next($request);
    }
}
