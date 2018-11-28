<?php

namespace App\Admin\Controllers;

use App\Models\ConsumerBank;
use App\Models\Bank;
use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;

class BankController extends Controller
{
    //用户银行卡首页
    public function index(Content $content)
    {
        return Admin::content(function (Content $content) {
            $content->header('用户银行卡列表');
            $content->body($this->grid());
        });
    }

    //编辑用户银行卡
    public function edit($id, Content $content)
    {
        return $content->header('用户银行卡')->description('编辑')->body($this->form()->edit($id));
    }

    //编辑用户
    public function update($id)
    {
        return $this->form()->update($id);
    }

    //获取用户数据
    protected function grid()
    {
        return Admin::grid(ConsumerBank::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->c_id('用户账号')->display(function ($value) {
                return DB::table('consumers')->where('id', $value)->value('mobile');
            });
            $grid->bank_name('银行卡名称');
            $grid->bank_card('银行卡号');
            $grid->create_time('创建时间');
            $grid->is_del('是否删除')->display(function ($value) {
                return ($value==1) ? '是' : '否';
            });
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
        return Admin::form(ConsumerBank::class, function (Form $form) {
            $form->select('bank_name','银行卡名称')->options(function ($name) {
                $bank = DB::table('banks')->where('is_del', 1)->select('name','abbreviation')->get();
                $array = [];
                if ($bank) {
                    foreach ($bank as $k => $v) {
                        $array[$v->abbreviation] = $v->name;
                    }
                }
                return $array;
            });
            $form->text('bank_card', '银行卡号')->rules('required');
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
