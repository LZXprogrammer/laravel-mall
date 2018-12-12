<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Good;
use App\Models\GoodSku;
use App\Models\Consumer;
use App\Models\ConsumerAccount;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yansongda\Supports\Log;

class PayController extends Controller
{
    /**
     * 支付宝支付
     *
     * @param   order_id        string          订单ID
     * @param           string          用户ID
     * @return \Illuminate\Http\Response
     */
    public function aliPay(Request $request)
    {
        //获取参数
        $order_id = $request->get('id');

        //判断订单是否属于用户
        $order = $this->checkOrder($order_id);

        if($order == '订单不存在' || $order == '订单状态不正确') {
            return ['code'=>'0', 'message'=>$order, 'data'=>''];
        }
        //生成请求数据
        $request_data = [
            'body'         => $order['body'],
            'subject'      => 'POS支付商城',
            'out_trade_no' => $order['no'],
            'total_amount' => $order['total_amount'],
            'goods_type'   => '1',
        ];
        //发送请求
        return app('alipay')->wap($request_data);
    }

    public function alipayReturn()
    {
        // 校验提交的参数是否合法
        $data = app('alipay')->verify();
        return $data;
    }

    //服务器端回调
    public function alipayNotify()
    {
        $data = app('alipay')->verify();
        //记录日志
        Log::info('Alipay notify', json_encode($data->all()));

        //支付宝返回数据校验
        $info = $data->all();
        Log::info('Alipay notify', $info['trade_status']);
        if($info['trade_status'] == 'TRADE_SUCCESS') {
            $this->successfulOrder($info['out_trade_no'], $info['trade_no'], 'alipay');
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
        $g_id = [];
        $body = '';
        foreach ($order['orderitems'] as $k => $v) {
            $g_id[] = $v['product_id'];
        }

        $good = Good::whereIn('id', $g_id)->select('name', 'serial_number')->get()->toArray();
        foreach ($good as $k => $v) {
            $body .= $v['name'].',';
        }
        $order['body'] = rtrim($body, ',');

        return $order;
    }

    private function successfulOrder($out_trade_no, $trade_no, $type) {
        $order = Order::where('no', $out_trade_no)->with('orderitems')->first()->toArray();
        if(!empty($out_trade_no) && $order['pay_status'] == 0 && $order['closed'] == 0) {
            //开始订单支付后操作

            //获取用户信息
            $user = Consumer::where('id', $order['c_id'])->first()->toArray();

            //查询三级用户
            $primary_distribution = $user['level_a'];
            $secondary_distribution = $user['level_b'];
            $three_distribution = $user['level_c'];

            //开启事务
            DB::beginTransaction();

            //处理订单状态
            $update = [
                'paid_time' => time(),
                'payment_method' => $type,
                'payment_no' => $trade_no,
                'pay_status' => '1',
            ];
            $os = Order::where('id', $order['id'])->update($update);
            if(!$os) {
                DB::rollBack();
                die;
            }

            $primary_money = $secondary_money = $three_money = 0;

            //处理返利
            foreach ($order['orderitems'] as $k => $v) {
                $info = GoodSku::where('id', $v['product_sku_id'])->with('good')->first()->toArray();
                //计算分销用户自增金额
                $primary_money += $info['market_a'] * $v['amount'];
                $secondary_money += $info['market_b'] * $v['amount'];
                $three_money += $info['market_c'] * $v['amount'];
            }

            //一级级分销用户金额修改
            if($primary_distribution != 0) {
                $primary_total = ConsumerAccount::where('id', $primary_distribution)->increment('total', $primary_money);
                $primary_available = ConsumerAccount::where('id', $primary_distribution)->increment('available', $primary_money);
                $primary_market = ConsumerAccount::where('id', $primary_distribution)->increment('market', $primary_money);
                $primary_market_a = ConsumerAccount::where('id', $primary_distribution)->increment('market_a', $primary_money);
                if(!$primary_total || !$primary_available || !$primary_market || !$primary_market_a) {
                    DB::rollBack();
                    die;
                }
            }

            //二级分销用户金额修改
            if($secondary_distribution != 0) {
                $secondary_total = ConsumerAccount::where('id', $secondary_distribution)->increment('total', $secondary_money);
                $secondary_available = ConsumerAccount::where('id', $secondary_distribution)->increment('available', $secondary_money);
                $secondary_market = ConsumerAccount::where('id', $secondary_distribution)->increment('market', $secondary_money);
                $secondary_market_b = ConsumerAccount::where('id', $secondary_distribution)->increment('market_b', $secondary_money);
                if(!$secondary_total || !$secondary_available || !$secondary_market || !$secondary_market_b) {
                    DB::rollBack();
                    die;
                }
            }

            //三级分销用户金额修改
            if($three_distribution != 0) {
                $three_total = ConsumerAccount::where('id', $three_distribution)->increment('total', $three_money);
                $three_available = ConsumerAccount::where('id', $three_distribution)->increment('available', $three_money);
                $three_market = ConsumerAccount::where('id', $three_distribution)->increment('market', $three_money);
                $three_market_c = ConsumerAccount::where('id', $three_distribution)->increment('market_c', $three_money);
                if(!$three_total || !$three_available || !$three_market || !$three_market_c) {
                    DB::rollBack();
                    die;
                }
            }

            DB::commit();
        }
    }
}
