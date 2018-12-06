<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersListController extends Controller
{
    /**
     * 会员列表
     *
     * @return array
     */
    public function index(Request $request)
    {
        //获取参数
        $level = $request->post('level');
        $page = $request->post('page');

        //进行判断是几级会员
        switch ($level) {
            case '1':

                break;
            case '2':
                break;
            case '3':
                break;
        }
    }


}
