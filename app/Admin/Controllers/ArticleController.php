<?php

namespace App\Admin\Controllers;

use App\Models\Article;
use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    //文章首页
    public function index(Content $content)
    {
        return Admin::content(function (Content $content) {
            $content->header('文章列表');
            $content->body($this->grid());
        });
    }

    //编辑文章
    public function edit($id, Content $content)
    {
        return $content->header('文章')->description('编辑')->body($this->form()->edit($id));
    }

    //编辑文章
    public function update($id)
    {
        return $this->form()->update($id);
    }

    //添加文章
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header('创建信用卡类别');
            $content->body($this->form());
        });
    }


    //添加文章保存回调
    public function store()
    {
        return $this->form()->store();
    }

    //获取文章
    protected function grid()
    {
        return Admin::grid(Article::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->name('文章名称')->style('max-width:200px;word-break:break-all;');
            $grid->type('文章类别')->display(function ($value) {
                return ($value == '1') ? 'banner' : '指南链接';
            });
            $grid->url('跳转url')->style('max-width:300px;word-break:break-all;');
            $grid->show_time('显示时间')->display(function ($value) {
                return date('Y-m-d H:i:s', $value);
            });
            $grid->end_time('结束时间')->display(function ($value) {
                return date('Y-m-d H:i:s', $value);
            });
            $grid->is_show('是否显示')->display(function ($value) {
                return ($value==1) ? '是' : '否';
            });
        });
    }

    //修改表单事项
    protected function form()
    {
        // 创建一个表单
        return Admin::form(Article::class, function (Form $form) {
            $form->text('name', '文章名称')->rules('required');
            $form->text('blurb', '文章简介')->rules('required');
            $form->image('picture', '展示图片')->rules('image');
            $form->select('type', '文章类别')->options(['1' => 'banner', '2' => '指南链接']);
            $form->url('url', '跳转url');
            $form->editor('content', '文章内容');
            $form->datetimeRange('show_time', 'end_time', '显示时间');
            $form->radio('is_show', '是否显示')->options(['1' => '是', '0'=> '否'])->default('0');


            // 定义事件回调，当模型即将保存时会触发这个回调
            $form->saving(function (Form $form) {
                $form->show_time = strtotime($form->show_time);
                $form->end_time = strtotime($form->end_time);
            });
            $form->tools(function (Form\Tools $tools) {
                // 去掉`删除`按钮
                $tools->disableDelete();
            });
        });
    }
}
