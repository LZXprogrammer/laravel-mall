<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class UserOrderController extends Controller
{
    /**
     * 用户订单列表
     *
     * @return array()
     */
    public function index(Request $request)
    {
        $status = $request->post('status');

        $info = array();
        switch ($status) {
            case 'all':
                $info = Order::where('c_id', session('uid'))->select('id')->get()->toArray();
                break;
            case '0':
                $info = Order::where('c_id', session('uid'))->where('pay_status', $status)->select('id')->get()->toArray();
                break;
            case '1':
                $info = Order::where('c_id', session('uid'))->where('pay_status', $status)->select('id')->get()->toArray();
                break;
            case '2':
                $info = Order::where('c_id', session('uid'))->where('pay_status', $status)->select('id')->get()->toArray();
                break;
            case '3':
                $info = Order::where('c_id', session('uid'))->where('pay_status', $status)->select('id')->get()->toArray();
                break;
            case '5':
                $info = Order::where('c_id', session('uid'))->where('pay_status', $status)->select('id')->get()->toArray();
                break;
        }
        foreach ($info as $k => $v) {
            $id[] = $v['id'];
        }
        $info = DB::table('orders')->whereIn('c_id', session('uid'))->leftJoin('orders.id')->get();

        return $info;
    }
}
