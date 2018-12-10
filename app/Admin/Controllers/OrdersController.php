<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Controllers\ModelForm;

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
                return DB::table('consumer')->where('id', $value)->value('name');
            });

            $grid->extra('其他额外的数据');
            $grid->create_time('创建时间')->display(function ($value) {
                return date("Y-m-d H:i:s", $value);
            });

            $grid->total_amount('订单总金额')->sortable();
            $grid->pay_status('支付状态');
            $grid->payment_method('支付方式');
            $grid->closed('订单状态')->display(function ($value) {
                return ($value==1) ? '已关闭' : '未关闭';
            });

            $grid->paid_time('创建时间')->display(function ($value) {
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
}