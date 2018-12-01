<?php

namespace App\Admin\Controllers;

use App\Models\CreditType;
use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class CreditTypeController extends Controller
{
    //信用卡类别列表首页
    public function index(Content $content)
    {
        return Admin::content(function (Content $content) {
            $content->header('信用卡类别列表');
            $content->body($this->grid());
        });
    }

    //编辑信用卡类别
    public function edit($id, Content $content)
    {
        return $content->header('信用卡类别信息')->description('编辑')->body($this->form()->edit($id));
    }

    //编辑信用卡类别
    public function update($id)
    {
        return $this->form()->update($id);
    }

    //编辑信用卡类别
    public function delete($id)
    {
        var_dump($id);exit;
    }

    //添加信用卡类别模板
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header('创建信用卡类别');
            $content->body($this->form());
        });
    }


    //添加信用卡类别保存回调
    public function store()
    {
        return $this->form()->store();
    }

    //获取信用卡类别数据
    protected function grid()
    {
        return Admin::grid(CreditType::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->name('信用卡类别名称');
            $grid->create_time('添加时间')->display(function ($value) {
                return date('Y-m-d H:i:s', $value);
            });
            $grid->is_enable('是否启用')->display(function ($value) {
                return ($value == '1') ? '启用' : '禁用';
            });
            $grid->actions(function ($actions) {
                $actions->disableView();
            });
        });
    }

    //修改表单事项
    protected function form()
    {
        // 创建一个表单
        return Admin::form(CreditType::class, function (Form $form) {
            $form->text('name', '信用卡类别名称')->rules('required');
            $form->select('is_enable','是否启用')->options(['0' => '禁用', '1' => '启用'])->default(1);
            $form->hidden('create_time')->default(time());

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
