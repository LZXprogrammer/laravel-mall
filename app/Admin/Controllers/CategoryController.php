<?php

namespace App\Admin\Controllers;

use App\Models\GoodCategory;
use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class CategoryController extends Controller
{
    //商品系列
    public function index(Content $content)
    {
        return Admin::content(function (Content $content) {
            $content->header('商品系列');
            $content->body($this->grid());
        });
    }

    //编辑商品系列
    public function edit($id, Content $content)
    {
        return $content->header('商品系列')->description('编辑')->body($this->form()->edit($id));
    }

    //编辑商品系列
    public function update($id)
    {
        return $this->form()->update($id);
    }

    //添加商品
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header('创建商品系列');
            $content->body($this->form());
        });
    }

    //添加商品保存回调
    public function store()
    {
        return $this->form()->store();
    }

    //获取商品系列数据
    protected function grid()
    {
        return Admin::grid(GoodCategory::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->name('系列名称');
            $grid->picture('系列图片')->image();
            $grid->is_del('是否删除')->display(function ($value) {
                return ($value==1) ? '未删除' : '已删除';
            });

            $grid->actions(function ($actions) {
                $actions->disableView();
            });

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
        return Admin::form(GoodCategory::class, function (Form $form) {
            $form->text('name', '系列名称')->rules('required');
            $form->image('picture', '系列图片')->rules('image');
            $form->radio('is_del', '是否删除')->options(['1' => '未删除', '0'=> '已删除'])->default('1');


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
