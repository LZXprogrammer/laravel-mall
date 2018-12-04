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
    // public function handle($request, Closure $next)
    // {
    //     $response = $next($request);
    //     // $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
    //     // $allow_origin = [
    //     //     '*',
    //     // ];
    //     // if (in_array($origin, $allow_origin)) {
    //         // $response->header('Access-Control-Allow-Origin', $origin);
    //         $response->header('Access-Control-Allow-Origin', '*');
    //         $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, X-CSRF-TOKEN, X-Requested-With, Accept, Authorization, X-XSRF-TOKEN');
    //         //$response->header('Access-Control-Expose-Headers', 'Authorization, authenticated');
    //         $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT');
    //         $response->header('Access-Control-Allow-Credentials', 'false');
    //     // }
    //     return $next($request);
    // }

    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->header('Access-Control-Allow-Headers', 'Origin, application/x-www-form-urlencoded, Content-Type, Cookie, X-CSRF-TOKEN, Authorization, Accept');
        // response allow return Authorization header
        $response->header('Access-Control-Expose-Headers', 'Authorization');
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->header('Access-Control-Max-Age', '3628800');

        if ($request->server('REQUEST_METHOD') == 'OPTIONS') {
            $response->header('status', '200 ok');
        }

        return $response;
    }
}
