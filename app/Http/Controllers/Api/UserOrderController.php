<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Config;

class UserOrderController extends Controller
{
    /**
     * 用户订单列表
     *
     * @param   uid         string      用户ID
     * @param   status         string      订单状态
     * @param   page         string      页数
     * @return array()
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $page = $request->get('page');

        $pages = Config::get('systems.defaultPage');
        $info = $id = $items = [];
        $ret = ['current_page'=>$page,'data'=>array()];

        switch ($status) {
            case 'all':
                $info = Order::where('c_id', session('uid'))->where('is_del', '1')->select('id')
                    ->simplePaginate($pages)->toArray();
                break;
            case '0':
                $info = Order::where('c_id', session('uid'))->where('pay_status', '0')->where('is_del', '1')
                    ->select('id')->simplePaginate($pages)->toArray();
                break;
            case '1':
                $info = Order::where('c_id', session('uid'))->where('pay_status', '1')->where('is_del', '1')
                    ->select('id')->simplePaginate($pages)->toArray();
                break;
            case '2':
                $info = Order::where('c_id', session('uid'))->where('pay_status', '2')->where('is_del', '1')
                    ->select('id')->simplePaginate($pages)->toArray();
                break;
            case '3':
                $info = Order::where('c_id', session('uid'))->where('pay_status', '3')->where('is_del', '1')
                    ->select('id')->simplePaginate($pages)->toArray();
                break;
            case '4':
                $info = Order::where('c_id', session('uid'))->where('pay_status', '5')->where('is_del', '1')
                    ->select('id')->simplePaginate($pages)->toArray();
                break;
        }
        //用户暂无订单数据
        if(empty($info['data'])) {
            return ['code'=>1,'message'=>'请求成功','data'=>$ret];
        }
        foreach ($info['data'] as $k => $v) {
            $id[] = $v['id'];
        }
        //查询数据
        $list = OrderItem::whereIn('order_id', $id)->with('goods', 'goods_sku', 'orders')->get()->toArray();
        //遍历数据
        foreach ($list as $k => $v) {
            //订单数据
            $items[$v['orders']['id']]['id'] = $v['orders']['id'];            //订单ID
            $items[$v['orders']['id']]['no'] = $v['orders']['no'];            //订单编号
            $items[$v['orders']['id']]['status'] = $v['orders']['pay_status'];      //订单状态
            $items[$v['orders']['id']]['total_amount'] = $v['orders']['total_amount'];        //订单总价


            $res['goods_id'] = $v['goods']['id'];           //商品名称
            $res['goods_sku_id'] = $v['goods_sku']['id'];           //商品名称
            $res['goods_name'] = $v['goods']['name'];           //商品名称
            $res['category'] = ($v['goods']['category'] == '1') ? '企业POS机' : '个人POS机';      //商品类别
            $res['trad_channel'] = $v['goods_sku']['trad_channel'];         //商品通道
            $res['show_pic'] = $v['goods']['show_pic'];         //商品通道
            $res['amount'] = $v['amount'];                //订单总数
            $res['price'] = $v['price'];          //单价
            $items[$v['orders']['id']]['goods_sku'][] = $res;
        }
        $ret['data'] = $items;

        return ['code'=>1,'message'=>'请求成功','data'=>$ret];
    }

    /**
     * 用户取消订单
     *
     * @param   id      string      订单ID
     * @param   uid      string      用户ID
     * @param   reason      string      取消订单原因
     * @return array()
     */
    public function cancelOrder(Request $request)
    {
        //获取参数
        $id = $request->get('id');
        $reason = $request->get('reason');

        $order = Order::where('id', $id)->where('c_id', session('uid'))->where('closed', '0')->first();
        if(empty($order)) {
            return ['code'=>'0', 'message'=>'订单不存在', 'data'=>''];
        }
        if($order->pay_status > 0) {
            return ['code'=>'0', 'message'=>'订单已成功,请走退款流程', 'data'=>''];
        }

        $res = Order::where('id', $id)->where('c_id', session('uid'))->update(['closed'=>'1', 'closed_reason'=>$reason]);
        if(!$res) {
            return ['code'=>'0', 'message'=>'取消订单失败', 'data'=>''];
        }
        return ['code'=>'1', 'message'=>'取消订单成功', 'data'=>''];
    }

    /**
     * 用户删除订单
     *
     * @param   id      string      订单ID
     * @param   uid      string      用户ID
     * @return array()
     */
    public function delOrder(Request $request)
    {
        //获取参数
        $id = $request->get('id');

        $order = Order::where('id', $id)->where('c_id', session('uid'))->where('is_del', '0')->first();
        if(empty($order)) {
            return ['code'=>'0', 'message'=>'订单不存在', 'data'=>''];
        }
        if($order->pay_status < 3) {
            return ['code'=>'0', 'message'=>'订单未完成，暂时不能删除', 'data'=>''];
        }

        $res = Order::where('id', $id)->where('c_id', session('uid'))->update(['is_del'=>'0']);
        if(!$res) {
            return ['code'=>'0', 'message'=>'取消订单失败', 'data'=>''];
        }
        return ['code'=>'1', 'message'=>'取消订单成功', 'data'=>''];
    }

    /**
     * 用户订单详情
     *
     * @param   id      string      订单ID
     * @param   uid      string      用户ID
     * @return array()
     */
    public function detailOrder(Request $request)
    {
        //获取参数
        $id = $request->get('id');

        $order = Order::where('id', $id)->where('c_id', session('uid'))->first();
        if(empty($order)) {
            return ['code'=>'0', 'message'=>'订单不存在', 'data'=>''];
        }

        $items = [];

        //查询数据
        $list = OrderItem::where('order_id', $id)->with('goods', 'goods_sku', 'orders')->get()->toArray();
        //遍历数据
        foreach ($list as $k => $v) {
            //订单数据
            $items['id'] = $v['orders']['id'];            //订单ID
            $items['no'] = $v['orders']['no'];            //订单编号
            $items['status'] = $v['orders']['pay_status'];      //订单状态
            $items['total_amount'] = $v['orders']['total_amount'];        //订单总价


            $res['goods_id'] = $v['goods']['id'];           //商品名称
            $res['goods_sku_id'] = $v['goods_sku']['id'];           //商品名称
            $res['goods_name'] = $v['goods']['name'];           //商品名称
            $res['category'] = ($v['goods']['category'] == '1') ? '企业POS机' : '个人POS机';      //商品类别
            $res['trad_channel'] = $v['goods_sku']['trad_channel'];         //商品通道
            $res['show_pic'] = $v['goods']['show_pic'];         //商品通道
            $res['amount'] = $v['amount'];                //订单总数
            $res['price'] = $v['price'];          //单价
            $items['goods_sku'][] = $res;
        }

        return ['code'=>1,'message'=>'请求成功','data'=>$items];
    }

    /**
     * 确认订单
     *
     * @param   id      string      订单ID
     * @param   uid      string      用户ID
     * @return array()
     */
    public function confirmOrder(Request $request)
    {
        //获取参数
        $id = $request->get('id');

        $order = Order::where('id', $id)->where('c_id', session('uid'))->first();
        if(empty($order)) {
            return ['code'=>'0', 'message'=>'订单不存在', 'data'=>''];
        }
        if($order->pay_status < 2) {
            return ['code'=>'0', 'message'=>'该订单未发货，暂不能确认收货', 'data'=>''];
        }
        if($order->pay_status > 2) {
            return ['code'=>'0', 'message'=>'该订单已确认收货', 'data'=>''];
        }

        $res = Order::where('id', $id)->where('c_id', session('uid'))->update(['pay_status'=>'3']);
        if(!$res) {
            return ['code'=>'0', 'message'=>'取消订单失败', 'data'=>''];
        }
        return ['code'=>'1', 'message'=>'取消订单成功', 'data'=>''];
    }
}
