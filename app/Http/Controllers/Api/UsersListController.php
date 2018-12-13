<?php

namespace App\Http\Controllers\Api;

use App\Models\Consumer;
use App\Models\Good;
use App\Models\GoodSku;
use App\Models\ImportOrder;
use App\Models\ImportUserAccount;
use App\Models\ImportDistributionRecord;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

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
        $uid = $this->queryUsers();

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
        $beginTime = $request->get('begin_time');
        $endTime = $request->get('end_time');
        $sku_id = $request->get('sku_id');
        $level = $request->get('level');
        $mobile = $request->get('mobile');

        //查询用户三级用户ID
        $uid = $this->queryUsers($level);

        //获取默认每页数量
        $page = Config::get('systems.defaultPage');
        //查询数据
        $list = ImportOrder::whereIn('c_id', $uid)
                            ->when($beginTime, function ($query) use ($beginTime) {
                                return $query->where('trading_time', '>=', $beginTime);
                            })->when($endTime, function ($query) use ($endTime) {
                                return $query->where('trading_time', '<=', $endTime);
                            })->when($sku_id, function ($query) use ($sku_id) {
                                return $query->where('sku_id', $sku_id);
                            })->when($mobile, function ($query) use ($mobile) {
                                return $query->where('mobile', 'like',$mobile);
                            })->select(DB::raw('count(id) as total, good_name, sku_name'))
                            ->groupBy('sku_id')->simplePaginate($page)->toArray();

        $info['list'] = $list['data'];
        $info['current_page'] = $list['current_page'];
        return ['code' => 1, 'message' => '请求成功', 'data' => $info];
    }

    /**
     * 商品信息查询
     *
     * @return array
     */
    public function dropDown(Request $request)
    {
        //获取订单ID
        $order_id = $request->get('order_id');

        if(!empty($order_id)) {
            $list = GoodSku::where('g_id', $order_id)->select('id', 'trad_channel as name')->get()->toArray();
        }else{
            $list = Good::where('is_del', 1)->select('id', 'name')->get()->toArray();
        }

        return ['code' => 1, 'message' => '请求成功', 'data' => $list];
    }

    /**
     * 收益信息
     *
     * @param   uid     string      用户ID
     * @return array
     */
    public function earnings()
    {
        //用户账户总额
        $account = ImportUserAccount::where('c_id', session('uid'))->select('total', 'available', 'withdraw')->first()->toArray();
        //查询用户三级用户ID
        $uid = $this->queryUsers();

        //总收益
        $info['total_volume'] = ImportDistributionRecord::whereIn('agency_uid', $uid)->sum('agency_amount');

        //获取昨日起始时间戳和结束时间戳
        $beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
        $endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
        $info['yesterday_volume'] = ImportDistributionRecord::whereIn('agency_uid', $uid)->where('create_time', '>=', $beginYesterday)->where('create_time', '<=', $endYesterday)->sum('agency_amount');

        //获取本月起始时间戳和结束时间戳
        $beginThisMonth=mktime(0,0,0,date('m'),1,date('Y'));
        $endThisMonth=mktime(23,59,59,date('m'),date('t'),date('Y'));
        $info['this_month_volume'] = ImportDistributionRecord::whereIn('agency_uid', $uid)->where('create_time', '>=', $beginThisMonth)->where('create_time', '<=', $endThisMonth)->sum('agency_amount');

        //获取三个月起始时间戳和结束时间戳
        $beginThreeMonth=mktime(0,0,0,date('m')-2,1,date('Y'));
        $endThreeMonth=mktime(23,59,59,date('m'),date('t'),date('Y'));
        $info['three_month_volume'] = ImportDistributionRecord::whereIn('agency_uid', $uid)->where('create_time', '>=', $beginThreeMonth)->where('create_time', '<=', $endThreeMonth)->sum('agency_amount');

        //返回信息
        $info['account'] = $account;
        return ['code' => 1, 'message' => '请求成功', 'data' => $info];
    }

    /**
     * 收益信息查询
     *
     * @param   uid     string      用户ID
     * @return array
     */
    public function earningsQuery(Request $request)
    {
        //获取参数
        $beginTime = $request->get('begin_time');
        $endTime = $request->get('end_time');
        $sku_id = $request->get('sku_id');
        $level = $request->get('level');
        $mobile = $request->get('mobile');

        //查询用户三级用户ID
        $uid = $this->queryUsers($level);

        //获取默认每页数量
        $page = Config::get('systems.defaultPage');
        //查询数据
        $list = ImportDistributionRecord::whereIn('agency_uid', $uid)
            ->when($beginTime, function ($query) use ($beginTime) {
                return $query->where('create_time', '>=', $beginTime);
            })->when($endTime, function ($query) use ($endTime) {
                return $query->where('create_time', '<=', $endTime);
            })->when($sku_id, function ($query) use ($sku_id) {
                return $query->where('sku_id', $sku_id);
            })->when($mobile, function ($query) use ($mobile) {
                return $query->where('mobile', 'like',$mobile);
            })->select(DB::raw('SUM(agency_amount) as total, good_name, sku_name'))
            ->groupBy('sku_id')->simplePaginate($page)->toArray();

        $info['list'] = $list['data'];
        $info['current_page'] = $list['current_page'];
        return ['code' => 1, 'message' => '请求成功', 'data' => $info];
    }

    /**
     * 获取用户ID
     *
     * @return array
     */
    private function queryUsers($level = 'all') {
        switch ($level) {
            case '1':
                return Consumer::where('level_a', session('uid'))->select('id')->get()->toArray();
                break;
            case '2':
                return Consumer::where('level_b', session('uid'))->select('id')->get()->toArray();
                break;
            case '3':
                return Consumer::where('level_c', session('uid'))->select('id')->get()->toArray();
                break;
            default:
                return Consumer::where('level_a', session('uid'))->orWhere('level_b', session('uid'))->orWhere('level_c', session('uid'))->select('id')->get()->toArray();
                break;
        }
    }
}
