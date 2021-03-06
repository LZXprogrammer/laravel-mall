<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Good;
use App\Models\GoodSku;
use App\Models\Consumer;
use App\Models\ConsumerAccount;
use App\Models\DistributionRecord;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayController extends Controller
{
    /**
     * 支付宝支付
     *
     * @param  string  order_id 订单ID
     * @param  string  用户ID
     * @return \Illuminate\Http\Response
     */
    public function aliPay(Request $request)
    {
        //获取参数
        $order_id = $request->get('id');

        //判断订单是否属于用户
        $order = $this->checkOrder($order_id);

        if($order == '订单不存在' || $order == '订单状态不正确') {
            return ['code' => 0, 'message' => $order, 'data' => ''];
        }
        //生成请求数据
        $request_data = [
            'subject'      => 'POS支付商城',
            'out_trade_no' => $order['no'],
            'total_amount' => $order['total_amount'],
        ];
        //发送请求
        return app('alipay')->wap($request_data);
    }

    // 客户端回调 —— 同步回调
    public function alipayReturn()
    {
        // 校验提交的参数是否合法
        $data = app('alipay')->verify();
        return $data;
    }

    //服务器端回调 —— 异步回调
    public function alipayNotify()
    {
        $data = app('alipay')->verify();
        //记录日志
        Log::info($data->all());

        //支付宝返回数据校验
        $info = $data->all();
        Log::info('Alipay notify', ['trade_status'=>$info['trade_status'],'out_trade_no'=>$info['out_trade_no'],'trade_no'=>$info['trade_no']]);
        if($info['trade_status'] == 'TRADE_SUCCESS') {
            $order = $this->successfulOrder($info['out_trade_no'], $info['trade_no'], 'alipay');
            if($order) {
                return 'SUCCESS';
            }
        }
    }

    //校验用户是否存在
    private function checkOrder($order_id) {
        //获取订单参数
        $order = Order::where('id', $order_id)->where('c_id', session('uid'))->with('orderitems')->first()->toArray();
        //订单是否存在
        if(empty($order)) {
            return '订单不存在';
        }
        //订单是否正确
        if($order['pay_status'] != 0 || $order['closed'] == 1) {
            return '订单状态不正确';
        }
        //获取信息

        return $order;
    }

    //处理订单
    private function successfulOrder($out_trade_no, $trade_no, $type) {
        Log::info('successfulOrder notify', ['trade_status'=>$out_trade_no,'out_trade_no'=>$trade_no,'trade_no'=>$type]);
        $order = Order::where('no', $out_trade_no)->with('orderitems')->first()->toArray();

        
        //开始订单支付后操作
        if(!empty($order)) {
            //开启事务
            DB::beginTransaction();

            // 处理加入会员订单状态
            $member_update = [
                'paid_time' => time(),
                'payment_method' => $type,
                'payment_no' => $trade_no,
                'pay_status' => 3,
            ];

            //处理商品订单状态
            $update = [
                'paid_time' => time(),
                'payment_method' => $type,
                'payment_no' => $trade_no,
                'pay_status' => 1,
            ];

            // 如果是加入会员的订单，那当前用户会员状态改为 已激活，订单状态直接改成 已完成=3 
            if($order['is_member_order']){

                $consumer = Consumer::where('id', $order['c_id'])->update(['is_active' => 1, 'active_time' => time()]);
                $order_member = Order::where('id', $order['id'])->update($member_update);
                // 两者都成功时提交事务
                if($consumer && $order_member){
                    DB::commit();
                }else{
                    DB::rollBack();
                }
                return false;

            }else{
                // 商品订单更改
                $os = Order::where('id', $order['id'])->update($update);
            }

            if(!$os) {
                DB::rollBack();
                return false;
            }

            //获取用户信息
            $user = Consumer::where('id', $order['c_id'])->first()->toArray();

            //查询三级用户
            $primary_distribution = $user['level_a'];
            $secondary_distribution = $user['level_b'];
            $three_distribution = $user['level_c'];

            //判断订单子表是否存在
            if(!empty($order['orderitems'])) {
                //处理返利
                $primary_money = $secondary_money = $three_money = 0;

                foreach ($order['orderitems'] as $k => $v) {
                    $info = GoodSku::where('id', $v['product_sku_id'])->with('good')->first()->toArray();
                    if(!empty($info)) {
                        //计算分销用户自增金额
                        $primary_money += $info['market_a'] * $v['amount'];
                        $secondary_money += $info['market_b'] * $v['amount'];
                        $three_money += $info['market_c'] * $v['amount'];
                    }
                }

                //一级级分销用户金额修改
                if($primary_distribution != 0) {
                    $primary_total = ConsumerAccount::where('id', $primary_distribution)->increment('total', $primary_money);
                    $primary_available = ConsumerAccount::where('id', $primary_distribution)->increment('freeze', $primary_money);
                    $primary_market = ConsumerAccount::where('id', $primary_distribution)->increment('market', $primary_money);
                    $primary_market_a = ConsumerAccount::where('id', $primary_distribution)->increment('market_a', $primary_money);
                    //插入分销记录表
                    DistributionRecord::insert([
                        'order_id' => $order['id'],
                        'agency_uid' => $primary_distribution,
                        'agency_amount' => $primary_money,
                        'level' => '1',
                        'create_time' => time()
                    ]);
                    if(!$primary_total || !$primary_available || !$primary_market || !$primary_market_a) {
                        DB::rollBack();
                        return false;
                    }
                }

                //二级分销用户金额修改
                if($secondary_distribution != 0) {
                    $secondary_total = ConsumerAccount::where('id', $secondary_distribution)->increment('total', $secondary_money);
                    $secondary_available = ConsumerAccount::where('id', $secondary_distribution)->increment('freeze', $secondary_money);
                    $secondary_market = ConsumerAccount::where('id', $secondary_distribution)->increment('market', $secondary_money);
                    $secondary_market_b = ConsumerAccount::where('id', $secondary_distribution)->increment('market_b', $secondary_money);
                    //插入分销记录表
                    DistributionRecord::insert([
                        'order_id' => $order['id'],
                        'agency_uid' => $secondary_distribution,
                        'agency_amount' => $secondary_money,
                        'level' => '2',
                        'create_time' => time()
                    ]);
                    if(!$secondary_total || !$secondary_available || !$secondary_market || !$secondary_market_b) {
                        DB::rollBack();
                        return false;
                    }
                }

                //三级分销用户金额修改
                if($three_distribution != 0) {
                    $three_total = ConsumerAccount::where('id', $three_distribution)->increment('total', $three_money);
                    $three_available = ConsumerAccount::where('id', $three_distribution)->increment('freeze', $three_money);
                    $three_market = ConsumerAccount::where('id', $three_distribution)->increment('market', $three_money);
                    $three_market_c = ConsumerAccount::where('id', $three_distribution)->increment('market_c', $three_money);
                    //插入分销记录表
                    DistributionRecord::insert([
                        'order_id' => $order['id'],
                        'agency_uid' => $three_distribution,
                        'agency_amount' => $three_money,
                        'level' => '3',
                        'create_time' => time()
                    ]);
                    if(!$three_total || !$three_available || !$three_market || !$three_market_c) {
                        DB::rollBack();
                        return false;
                    }
                }
            }

            Log::info('success');
            DB::commit();
            return true;
        }
    }
}
