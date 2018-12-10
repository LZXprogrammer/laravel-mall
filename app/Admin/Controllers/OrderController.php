<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class OrderController extends Controller
{
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
            $grid->name('产品名称');
            $grid->vender('厂家');
            $grid->category('产品类别')->display(function ($value) {
                return DB::table('good_categories')->where('id', $value)->value('name');
            });
            $grid->serial_number('产品序列号');
            $grid->price('产品价格');
            $grid->create_time('创建时间')->display(function ($value) {
                return date("Y-m-d H:i:s", $value);
            });
            $grid->courier_fees('快递费');
            $grid->is_show('是否显示')->display(function ($value) {
                return ($value==1) ? '是' : '否';
            });
            $grid->desc('排序')->sortable();

            $grid->tools(function ($tools) {
                // 禁用批量删除按钮
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
        });
    }
}