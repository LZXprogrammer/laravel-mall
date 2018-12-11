<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

class PayController extends Controller
{
    /**
     * 支付宝支付
     *
     * @return \Illuminate\Http\Response
     */
    public function aliPay(Request $request)
    {
        //获取参数
        $id = $request->get('id');

        //获取支付宝配置参数
        $config = Config::get('pay.alipay');

        $order = Order::where('id', $id)->with('orderitems')->first();

        $config_biz = [
            'out_trade_no' => $order->no,
            'total_amount' => '1',
            'subject'      => 'test-subject',
        ];
        $aliPay = app('alipay')->wap($config_biz);
        return $aliPay;
    }
}
