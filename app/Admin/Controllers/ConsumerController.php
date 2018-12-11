<?php

namespace App\Admin\Controllers;

use App\Models\Consumer;
use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;

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
            $grid->level_a('一级分销代理人')->display(function ($value) {
                return ($value == 0) ? '无代理人' : Consumer::where('id', $value)->value('mobile');
            });
            $grid->level_b('二级分销代理人')->display(function ($value) {
                return ($value == 0) ? '无代理人' : Consumer::where('id', $value)->value('mobile');
            });
            $grid->level_c('三级分销代理人')->display(function ($value) {
                return ($value == 0) ? '无代理人' : Consumer::where('id', $value)->value('mobile');
            });
            $grid->create_time('注册时间')->display(function ($value) {
                return (empty($value)) ? '' : date('Y-m-d H:i:s', $value);
            });
            $grid->real_time('实名时间')->display(function ($value) {
                return (empty($value)) ? '' : date('Y-m-d H:i:s', $value);
            });
            $grid->active_time('激活时间')->display(function ($value) {
                return (empty($value)) ? '' : date('Y-m-d H:i:s', $value);
            });

            $grid->actions(function ($actions) {
                $actions->disableDelete();
            });

            $grid->filter(function($filter){

                // 去掉默认的id过滤器
                $filter->disableIdFilter();

                // 在这里添加字段过滤器
                $filter->like('mobile', '用户账号');
                $filter->like('real_name', '真实姓名');

                $filter->equal('create_time')->datetime();
            });

            $grid->model()->orderBy('create_time', 'desc');

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
            $form->text('nick_name', '用户昵称');
            $form->text('real_name', '用户真实姓名');
            $form->text('id_number', '用户身份证号');
            $form->image('avatar', '用户头像')->rules('image');
            $form->radio('is_active', '是否激活')->options(['1' => '是', '0'=> '否'])->default('0');
            $form->text('promote', '推广码');


            // 定义事件回调，当模型即将保存时会触发这个回调
            $form->saving(function (Form $form) {
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

        $show->mobile('用户账号');
        $show->avatar('用户头像')->image();
        $show->nick_name('用户昵称');
        $show->real_name('真实姓名');
        $show->id_number('身份证号');
        $show->is_active('是否激活')->using(['0' => '否', '1' => '是']);
        $show->level_a('一级分销代理人')->as(function ($value) {
            return ($value == 0) ? '无代理人' : Consumer::where('id', $value)->value('mobile');
        });
        $show->level_b('二级分销代理人')->as(function ($value) {
            return ($value == 0) ? '无代理人' : Consumer::where('id', $value)->value('mobile');
        });
        $show->level_c('三级分销代理人')->as(function ($value) {
            return ($value == 0) ? '无代理人' : Consumer::where('id', $value)->value('mobile');
        });
        $show->create_time('创建时间')->as(function ($value) {
            return date('Y-m-d H:i:s', $value);
        });
        $show->real_time('实名时间')->as(function ($value) {
            return (empty($value)) ? '' : date('Y-m-d H:i:s', $value);
        });
        $show->active_time('激活时间')->as(function ($value) {
            return (empty($value)) ? '' : date('Y-m-d H:i:s', $value);
        });
        $show->promote('用户推广码');
        $show->panel()->tools(function ($tools) {
                $tools->disableDelete();
            });
        $show->account('用户资金信息', function ($account) {
            $account->setResource('/admin/consumer');
            $account->total('用户资金总额');
            $account->available('用户可用金额');
            $account->freeze('用户冻结金额');
            $account->withdraw('用户已提现总金额');
            $account->market('用户获得分销总金额');
            $account->market_a('用户获得一级分销代理人金额');
            $account->market_b('用户获得二级分销代理人金额');
            $account->market_c('用户获得三级分销代理人金额');
            $account->panel()->tools(function ($tools) {
                $tools->disableList();
                $tools->disableDelete();
                $tools->disableEdit();
            });
        });
        $show->bank('用户银行卡', function ($bank) {
            $bank->resource('/admin/comments');

            $bank->id('ID');
            $bank->bank_name('所属银行');
            $bank->bank_card('银行卡号');
            $bank->create_time('添加时间')->display(function ($value) {
                return (empty($value)) ? '' : date('Y-m-d H:i:s', $value);
            });
            $bank->is_del('是否删除')->using(['0' => '否', '1' => '是']);
            $bank->is_default('是否默认')->using(['0' => '否', '1' => '是']);
            $bank->disableCreateButton();
            $bank->disableExport();
            $bank->actions(function ($actions) {
                $actions->disableView();
                $actions->disableDelete();
                $actions->disableEdit();
            });
            $bank->tools(function ($tools) {
                // 禁用批量删除按钮
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
        });
        $show->address('用户地址', function ($address) {
            $address->resource('/admin/comments');

            $address->id('ID');
            $address->province_id('省')->display(function ($info) {
                return DB::table('areas')->where('ad_code', $info)->value('name');
            });
            $address->city_id('市')->display(function ($info) {
                return DB::table('areas')->where('ad_code', $info)->value('name');
            });;
            $address->area_id('区')->display(function ($info) {
                return DB::table('areas')->where('ad_code', $info)->value('name');
            });;
            $address->address('添加时间');
            $address->create_time('添加时间')->display(function ($value) {
                return (empty($value)) ? '' : date('Y-m-d H:i:s', $value);
            });
            $address->is_del('是否删除')->using(['0' => '否', '1' => '是']);
            $address->is_default('是否默认')->using(['0' => '否', '1' => '是']);
            $address->disableCreateButton();
            $address->disableExport();
            $address->actions(function ($actions) {
                $actions->disableView();
                $actions->disableDelete();
                $actions->disableEdit();
            });
            $address->tools(function ($tools) {
                // 禁用批量删除按钮
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
        });
        return $show;
    }
}
