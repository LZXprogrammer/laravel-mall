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
            returnJsonMsg('0', '用户旧密码不能为空', '');
        }
        if(empty($request->new_pwd)) {
            returnJsonMsg('0', '用户新密码不能为空', '');
        }
        if(empty($request->repeat_pwd)) {
            returnJsonMsg('0', '用户重复密码不能为空', '');
        }
        //判断两次密码是否正确
        if($request->new_pwd != $request->repeat_pwd) {
            return returnJsonMsg('0', '用户输入两次密码不一样', '');
        }

        return $next($request);
    }
}
