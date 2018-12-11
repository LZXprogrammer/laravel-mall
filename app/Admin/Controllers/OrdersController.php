<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Consumer;
use App\Models\Good;
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

    //查看详情
    // public function show($id, Content $content)
    // {
    //     return $content
    //         ->header('订单详情')
    //         ->description('详情')
    //         ->body($this->detail($id));
    // }

    public function show(Order $order)
    {
        return Admin::content(function (Content $content) use ($order) {
            $content->header('查看订单');

            // var_dump($order);die;
            $consumer = Consumer::where('id', $order->c_id)->select(['nick_name', 'mobile'])->first();

            // $aa = array_merge($order->toArray(), $consumer->toArray());
            
            $order_items = OrderItem::where('order_id', $order->id)->select(['product_id', 'product_sku_id', 'amount', 'price'])->get();
            // $products = [];
            // foreach ($order_items as $key => $order_item) {
                // $products[] = Good::where('id', $order_item->product_id)->with('sku')->first();
                // foreach ($products as $k => $product) {
                //     var_dump($product->sku);
                // }
                var_dump($consumer);
            // }
            
            die;


            // body 方法可以接受 Laravel 的视图作为参数
            $content->body(view('admin.orders.show', ['order' => $order]));
        });
    }

    /**
     * 获取订单数据
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

            $grid->extra('其他额外的数据');
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
                return date("Y-m-d H:i:s", $value);
            });

            $grid->tools(function ($tools) {
                // 禁用批量删除按钮
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
        });
    }

    /**
     * 商品详情
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Order::findOrFail($id));

        $show->id('ID');
        $show->no('订单号');
        $show->c_id('用户ID');
        $show->address('此订单收货地址')->as(function ($info) {
            // $info = json_decode($info, true);
            return $info;
        });
        $show->total_amount('此订单总金额');
        $show->remark('订单备注');
        $show->create_time('下单时间');
        $show->payment_no('支付交易号');
        $show->payment_method('支付方式');
        $show->paid_time('支付时间');
        $show->pay_status('订单状态');
        $show->closed_reason('订单取消原因');
        $show->ship_status('物流状态');
        $show->ship_data('物流数据');
        $show->delivery_time('发货时间');
        $show->clinch_time('成交时间');
        $show->reviewed('订单是否已评价');

        // return view('admin.orders.show', ['order' => $show->toArray()]);
        return $show;
    }
}