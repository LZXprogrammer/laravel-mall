<?php

namespace App\Http\Middleware;

use Closure;

class EnableCrossRequestMiddleware
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
        $response = $next($request);

        // $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
        // $allow_origin = [
            // 这里写允许的域名列表
            // eg: 'http://www.baidu.com',
        // ];
        // if (in_array($origin, $allow_origin)) {
            // $response->header('Access-Control-Allow-Origin', $origin);
            
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Headers', 'Origin, application/x-www-form-urlencoded, Content-Type, Cookie, X-CSRF-TOKEN, Authorization, Accept');
            // response allow return Authorization header
            // $response->header('Access-Control-Expose-Headers', 'Authorization');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->header('Access-Control-Max-Age', '3628800');

            // 如果下面为 true 时，'Access-Control-Allow-Origin' 的值不能为 * ，只能是请求域名
            // $response->header('Access-Control-Allow-Credentials', 'false');

            if ($request->server('REQUEST_METHOD') == 'OPTIONS') {
                $response->header('status', '200 ok');
            }

        // }

        return $response;
    }
}
