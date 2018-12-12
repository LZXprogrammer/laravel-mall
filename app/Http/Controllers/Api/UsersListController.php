<?php

namespace App\Http\Controllers\Api;

use App\Models\Consumer;
use App\Models\ImportOrder;
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

    /**
     * 交易信息
     *
     * @return array
     */
    public function trade()
    {
        //查询用户三级用户ID
        $uid = Consumer::where('level_a', session('uid'))->orWhere('level_b', session('uid'))->orWhere('level_c', session('uid'))->select('id')->get()->toArray();

        //总交易订单
        $info['total_volume'] = ImportOrder::whereIn('c_id', $uid)->count();

        //获取昨日起始时间戳和结束时间戳
        $beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
        $endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
        $info['yesterday_volume'] = ImportOrder::whereIn('c_id', $uid)->where('trading_time', '>=', $beginYesterday)->where('trading_time', '<=', $endYesterday)->count();

        //获取本月起始时间戳和结束时间戳
        $beginThisMonth=mktime(0,0,0,date('m'),1,date('Y'));
        $endThisMonth=mktime(23,59,59,date('m'),date('t'),date('Y'));
        $info['this_month_volume'] = ImportOrder::whereIn('c_id', $uid)->where('trading_time', '>=', $beginThisMonth)->where('trading_time', '<=', $endThisMonth)->count();

        //获取三个月起始时间戳和结束时间戳
        $beginThreeMonth=mktime(0,0,0,date('m')-2,1,date('Y'));
        $endThreeMonth=mktime(23,59,59,date('m'),date('t'),date('Y'));
        $info['three_month_volume'] = ImportOrder::whereIn('c_id', $uid)->where('trading_time', '>=', $beginThreeMonth)->where('trading_time', '<=', $endThreeMonth)->count();

        return ['code' => 1, 'message' => '请求成功', 'data' => $info];
    }

    /**
     * 交易信息查询
     *
     * @return array
     */
    public function tradeQuery(Request $request)
    {
        //获取参数
        $beginTime = $request->get('beginTime');
        $endTime = $request->get('endTime');
        $sku_id = $request->get('sku_id');
//        $level = $request->get('level');
        $mobile = $request->get('mobile');
        //拼接where条件
        $where = [];
        if(!empty($beginTime)) {
            $where[] = ['trading_time', '>=', $beginTime];
        }
        if(!empty($endTime)) {
            $where[] = ['trading_time', '<=', $endTime];
        }
        if(!empty($sku_id)) {
            $where[] = ['sku_id', '=', $sku_id];
        }
//        if(!empty($level)) {
//            $where[] = ['trading_time', '>=', $beginTime];
//        }
        if(!empty($mobile)) {
            $where[] = ['mobile', '=', $mobile];
        }
        //获取默认每页数量
        $page = Config::get('systems.defaultPage');
        //查询数据
        $list = ImportOrder::where($where)->simplePaginate($page)->toArray();
        return $list;
    }
}
