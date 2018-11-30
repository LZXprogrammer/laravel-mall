<?php

namespace App\Admin\Controllers;

use App\Models\MessageTemplate;
use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class CreditTypeController extends Controller
{
    //短信模板列表首页
    public function index(Content $content)
    {
        return Admin::content(function (Content $content) {
            $content->header('短信模板列表');
            $content->body($this->grid());
        });
    }

    //编辑短信模板
    public function edit($id, Content $content)
    {
        return $content->header('短信模板信息')->description('编辑')->body($this->form()->edit($id));
    }

    //编辑短信模板
    public function update($id)
    {
        return $this->form()->update($id);
    }

    //编辑短信模板
    public function delete($id)
    {
        var_dump($id);exit;
    }

    //添加商品短信模板
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header('创建短信模板');
            $content->body($this->form());
        });
    }


    //添加短信模板保存回调
    public function store()
    {
        return $this->form()->store();
    }

    //获取短信模板数据
    protected function grid()
    {
        return Admin::grid(MessageTemplate::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->name('模板名称');
            $grid->content('模板内容');
            $grid->create_time('添加时间')->display(function ($value) {
                return date('Y-m-d H:i:s', $value);
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
        return Admin::form(MessageTemplate::class, function (Form $form) {
            $form->text('name', '模板名称')->rules('required');
            $form->textarea('content', '模板内容')->rules('required')->help('模板内容自定义，需要验证码部分请用{code}替换；需要用户手机号部分请用{mobile}替换；需要用户姓名部分请用{real_name}替换；需要昵称部分请用{nick_name}替换；');
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
