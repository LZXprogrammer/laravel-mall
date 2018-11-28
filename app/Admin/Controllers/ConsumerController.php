<?php

namespace App\Admin\Controllers;

use App\Models\Consumer;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ConsumerController extends Controller
{
    //用户首页
    public function index(Content $content)
    {
        return Admin::content(function (Content $content) {
            $content->header('用户列表');
            $content->body($this->grid());
        });
    }

    //编辑用户
    public function edit($id, Content $content)
    {
        return $content->header('用户信息')->description('编辑')->body($this->form()->edit($id));
    }

    //编辑用户
    public function update($id)
    {
        return $this->form()->update($id);
    }

    //查看详情
    public function show($id, Content $content)
    {
        return $content
            ->header('用户信息')
            ->description('详情')
            ->body($this->detail($id));
    }

    //获取用户数据
    protected function grid()
    {
        return Admin::grid(Consumer::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->mobile('用户账号');
            $grid->nick_name('用户昵称');
            $grid->real_name('真实姓名');
            $grid->id_number('身份证号');
            $grid->is_active('是否激活')->display(function ($value) {
                return ($value==1) ? '是' : '否';
            });
            $grid->level_a('一级分销')->display(function ($value) {
                if($value == 0) {
                    return '无';
                }else{
                    $mobile = Consumer::where('id', $value)->first();
                    return $mobile->mobile;
                }
            });
            $grid->level_b('二级分销')->display(function ($value) {
                if($value == 0) {
                    return '无';
                }else{
                    $mobile = Consumer::where('id', $value)->first();
                    return $mobile->mobile;
                }
            });
            $grid->level_c('三级分销')->display(function ($value) {
                if($value == 0) {
                    return '无';
                }else{
                    $mobile = Consumer::where('id', $value)->first();
                    return $mobile->mobile;
                }
            });
            $grid->create_time('注册时间');
            $grid->real_time('实名时间');
            $grid->active_time('激活时间');

            $grid->actions(function ($actions) {
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
        return Admin::form(Consumer::class, function (Form $form) {
            $form->text('nick_name', '用户昵称')->rules('required');
            $form->text('real_name', '用户真实姓名')->rules('required');
            $form->text('id_number', '用户身份证号')->rules('required');
            $form->image('avatar', '用户头像')->rules('required|image');
            $form->radio('is_active', '是否激活')->options(['1' => '是', '0'=> '否'])->default('0');
            $form->text('promote', '推广码')->rules('required');


            // 定义事件回调，当模型即将保存时会触发这个回调
            $form->saving(function (Form $form) {
                // 修改上传目录

            });
            $form->tools(function (Form\Tools $tools) {
                // 去掉`删除`按钮
                $tools->disableDelete();
            });
        });
    }

    /**
     * 用户详情
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Consumer::findOrFail($id));

        $show->id('ID');
        $show->mobile('用户账号');
        $show->avatar('用户头像')->image();
        $show->nick_name('用户昵称');
        $show->real_name('真实姓名');
        $show->id_number('身份证号');
        $show->is_active('是否激活')->using(['0' => '否', '1' => '是']);
        $show->level_a('一级分销用户')->as(function ($info) {
            if($info == 0) {
                return '无';
            }else{
                $mobile = Consumer::where('id', $info)->first();
                return $mobile->mobile;
            }
        });
        $show->level_b('二级分销用户')->as(function ($info) {
            if($info == 0) {
                return '无';
            }else{
                $mobile = Consumer::where('id', $info)->first();
                return $mobile->mobile;
            }
        });
        $show->level_c('三级分销用户')->as(function ($info) {
            if($info == 0) {
                return '无';
            }else{
                $mobile = Consumer::where('id', $info)->first();
                return $mobile->mobile;
            }
        });
        $show->create_time('创建时间');
        $show->real_time('实名时间');
        $show->active_time('激活时间');
        $show->promote('用户推广码');
        $show->panel()->tools(function ($tools) {
                $tools->disableDelete();
            });;
        return $show;
    }
}
