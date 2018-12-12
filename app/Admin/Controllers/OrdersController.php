<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Consumer;
use App\Models\Good;
use App\Models\DistributionRecord;
use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class OrdersController extends Controller
{
    use ModelForm;
    
    /**
     * 订单列表
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return Admin::content(function (Content $content) {
            $content->header('订单列表');
            $content->body($this->grid());
        });
    }

    /**
     *  查看订单详情
     * 
     * @param Model $order
     * @return Array 
     */
    public function show(Order $order)
    {
        return Admin::content(function (Content $content) use ($order) {
            $content->header('查看订单');

            $order_infos = [];
            // 下单用户
            $consumer = Consumer::where('id', $order->c_id)->select(['nick_name', 'mobile', 'real_name'])->first();
            $order_infos['nick_name'] = $consumer->nick_name;
            $order_infos['real_name'] = $consumer->real_name;
            $order_infos['mobile'] = $consumer->mobile;
            
            // 订单字表联合商品表、商品字表查询
            $order_items = OrderItem::where('order_id', $order->id)->with('goods', 'goods_sku')->get();
            foreach ($order_items as $key => $order_item) {
                // var_dump($order_item->goods_sku);
                $order_infos['goods'][$order_item->product_sku_id]['price']  = $order_item->price;
                $order_infos['goods'][$order_item->product_sku_id]['amount'] = $order_item->amount;
                $order_infos['goods'][$order_item->product_sku_id]['name'] = $order_item->goods->name;
                $order_infos['goods'][$order_item->product_sku_id]['show_pic'] = $order_item->goods->show_pic;
                $order_infos['goods'][$order_item->product_sku_id]['g_id'] = $order_item->goods->id;
                $order_infos['goods'][$order_item->product_sku_id]['category'] = $order_item->goods->category;
                $order_infos['goods'][$order_item->product_sku_id]['g_sku_id'] = $order_item->goods_sku->id;
                $order_infos['goods'][$order_item->product_sku_id]['trad_channel'] = $order_item->goods_sku->trad_channel;
            }

            // 把数据库中地址 json 转成数组
            $order->address = json_decode($order->address, true);

            // 代理人信息
            $agent_infos = $users = [];
            $agent_records = DistributionRecord::where('order_id', $order->id)->with('consumer')->get();           
            foreach ($agent_records as $key => $agent_record) {
                // 用户存在才返回
                if($agent_record->consumer){
                    $users['id'] = $agent_record->consumer->id;
                    $users['mobile'] = $agent_record->consumer->mobile;
                    $users['nick_name'] = $agent_record->consumer->nick_name;
                    $users['agency_amount'] = $agent_record->agency_amount;

                    // 代理等级 1：一级代理 2：二级代理 3：三级代理
                    switch ($agent_record->level) {
                        case 1:
                            $agent_infos['primary'] = $users;
                            break;
                        case 2:
                            $agent_infos['second'] = $users;
                            break;
                        case 3:
                            $agent_infos['three'] = $users;
                            break;
                    }
                }             
            }
            // body 方法可以接受 Laravel 的视图作为参数
            $content->body(view('admin.orders.show', ['order' => array_merge($order_infos, $order->toArray()), 'agents' => $agent_infos]));
        });
    }

    /**
     * 获取订单数据列表
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Order::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->no('订单号');
            $grid->c_id('下单用户')->display(function ($value) {
                return Consumer::where('id', $value)->value('real_name');
            });

            $grid->create_time('下单时间')->display(function ($value) {
                return date("Y-m-d H:i:s", $value);
            });

            $grid->total_amount('订单总金额')->sortable();
            $grid->pay_status('支付状态')->display(function ($value){
                $pay_status = config('pos.pay_status');
                return $pay_status[$value];
            });
            $grid->payment_method('支付方式');
            $grid->closed('订单状态')->display(function ($value) {
                return ($value==1) ? '已关闭' : '未关闭';
            });

            $grid->paid_time('支付时间')->display(function ($value) {
                if($value){
                    return date("Y-m-d H:i:s", $value);
                }
                return $value;
                
            });

            // 数据查询过滤
            $grid->filter(function($filter){

                // 去掉默认的id过滤器
                // $filter->disableIdFilter();
            
                // 添加字段过滤器
                $filter->equal('no')->placeholder('请输入订单号');
                $filter->date('create_time', '下单时间');
            });

            // 禁用新增按钮
            $grid->disableCreateButton();

            // 禁用编辑和删除
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();              
            });

            $grid->tools(function ($tools) {
                // 禁用批量删除按钮
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
        });
    }

}