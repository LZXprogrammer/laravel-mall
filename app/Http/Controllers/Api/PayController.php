<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Good;
use App\Models\GoodSku;
use App\Http\Controllers\Controller;

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
        \Log::debug('Alipay notify', $data->all());
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
}
