<?php

namespace App\Admin\Controllers;

use App\Models\HarvestAddress;
use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    //用户地址首页
    public function index(Content $content)
    {
        return Admin::content(function (Content $content) {
            $content->header('用户收获地址列表');
            $content->body($this->grid());
        });
    }

    //编辑用户地址
    public function edit($id, Content $content)
    {
        return $content->header('用户收获地址')->description('编辑')->body($this->form()->edit($id));
    }

    //编辑用户地址
    public function update($id)
    {
        return $this->form()->update($id);
    }

    //获取用户地址
    protected function grid()
    {
        return Admin::grid(HarvestAddress::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->c_id('用户账号')->display(function ($value) {
                return DB::table('consumers')->where('id', $value)->value('mobile');
            });
            $grid->province_id('省')->display(function ($value) {
                return DB::table('areas')->where('ad_code', $value)->value('name');
            });
            $grid->city_id('市')->display(function ($value) {
                return DB::table('areas')->where('ad_code', $value)->value('name');
            });
            $grid->area_id('区')->display(function ($value) {
                return DB::table('areas')->where('ad_code', $value)->value('name');
            });
            $grid->address('详细地址');
            $grid->create_time('添加时间');
            $grid->is_default('是否默认')->display(function ($value) {
                return ($value==1) ? '是' : '否';
            });

            $grid->actions(function ($actions) {
                $actions->disableView();
                $actions->disableDelete();
            });

            $grid->disableCreateButton();

            $grid->tools(function ($tools) {
                // 禁用批量删除按钮
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
        });
    }

    //修改表单事项
    protected function form()
    {
        // 创建一个表单
        return Admin::form(HarvestAddress::class, function (Form $form) {
            $form->distpicker(['province_id', 'city_id', 'area_id'])->attribute('data-value-type', 'code');
            $form->text('address', '详细地址')->rules('required');
            $form->radio('is_del', '是否删除')->options(['1' => '未删除', '0'=> '已删除'])->default('0');
            $form->radio('is_default', '是否默认')->options(['1' => '是', '0'=> '否'])->default('0');


            // 定义事件回调，当模型即将保存时会触发这个回调
            $form->saving(function (Form $form) {
            });
            $form->tools(function (Form\Tools $tools) {
                // 去掉`删除`按钮
                $tools->disableDelete();
            });
        });
    }
}
