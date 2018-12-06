<?php

namespace App\Http\Middleware;

use Closure;

class CheckOldPassword
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
        if(empty($request->old_pwd)) {
            return response()->json(['code' => '0', 'message' => '用户旧密码不能为空', 'data' => '']);
        }
        if(empty($request->new_pwd)) {
            return response()->json(['code' => '0', 'message' => '用户新密码不能为空', 'data' => '']);
        }
        if(empty($request->repeat_pwd)) {
            return response()->json(['code' => '0', 'message' => '用户重复密码不能为空', 'data' => '']);
        }
        //判断两次密码是否正确
        if($request->new_pwd != $request->repeat_pwd) {
            return response()->json(['code' => '0', 'message' => '用户输入两次密码不一样', 'data' => '']);
        }

        return $next($request);
    }
}
