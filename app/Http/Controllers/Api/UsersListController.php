<?php

namespace App\Http\Controllers\Api;

use App\Models\Consumer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

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
        $level = $request->get('level');

        $page = Config::get('systems.defaultPage');
        $select = ['mobile', 'avatar', 'nick_name', 'create_time', 'is_active', 'active_time', 'promote'];
        $info = array();
        //进行判断是几级分销代理
        switch ($level) {
            case '1':
                $info = Consumer::where('level_a', session('uid'))->select($select)->simplePaginate($page);
                break;
            case '2':
                $info = Consumer::where('level_b', session('uid'))->select($select)->simplePaginate($page);
                break;
            case '3':
                $info = Consumer::where('level_c', session('uid'))->select($select)->simplePaginate($page);
                break;
        }
        return ['code' => '1', 'message' => '请求成功', 'data' => $info];
    }


}
