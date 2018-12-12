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

        $statistic = Consumer::where('id', session('uid'))->select(['total_users', 'membership_a', 'membership_b', 'membership_c'])->first();

        $user = array();
        //进行判断是几级分销代理
        switch ($level) {
            case '1':
                $user = Consumer::where('level_a', session('uid'))->select($select)->simplePaginate($page)->toArray();
                break;
            case '2':
                $user = Consumer::where('level_b', session('uid'))->select($select)->simplePaginate($page)->toArray();
                break;
            case '3':
                $user = Consumer::where('level_c', session('uid'))->select($select)->simplePaginate($page)->toArray();
                break;
        }
        //整理数据
        $info['statistic'] = $statistic;
        $info['list'] = $user['data'];
        $info['current_page'] = $user['current_page'];

        return ['code' => 1, 'message' => '请求成功', 'data' => $info];
    }
}
