<?php

namespace App\Admin\Controllers;

use App\Models\Message;
use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    //短信列表首页
    public function index(Content $content)
    {
        return Admin::content(function (Content $content) {
            $content->header('短信列表');
            $content->body($this->grid());
        });
    }

    //获取用户数据
    protected function grid()
    {
        return Admin::grid(Message::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->mobile('手机号');
            $grid->message_template()->name('模板名称');
            $grid->code('短信验证码');
            $grid->message('短信内容')->style('max-width:300px;word-break:break-all;');
            $grid->send_time('发送时间');
            $grid->overdue_time('过期时间');
            $grid->is_use('是否使用')->display(function ($value) {
                return ($value==1) ? '已使用' : '未使用';
            });
            $grid->type('短信类别')->display(function ($value) {
                return ($value==1) ? '模板短信' : '自定义短信';
            });

            $grid->actions(function ($actions) {
                $actions->disableView();
                $actions->disableDelete();
                $actions->disableEdit();
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
}
