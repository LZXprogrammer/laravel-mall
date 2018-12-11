<?php

namespace App\Admin\Controllers;

use App\Models\CreditCard;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;

class CreditCardController extends Controller
{
    use HasResourceActions;

    //短信模板列表首页
    public function index(Content $content)
    {
        return Admin::content(function (Content $content) {
            $content->header('信用卡列表');
            $content->body($this->grid());
        });
    }

    //编辑短信模板
    public function edit($id, Content $content)
    {
        return $content->header('信用卡信息')->description('编辑')->body($this->form()->edit($id));
    }

    //编辑短信模板
    public function update($id)
    {
        return $this->form()->update($id);
    }

    //添加商品短信模板
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header('创建信用卡');
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
        return Admin::grid(CreditCard::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->bank_name('所属银行');
            $grid->credit_type()->name('模板名称');
            $grid->name('信用卡名称');
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
        return Admin::form(CreditCard::class, function (Form $form) {
            $form->select('bank_name', '所属银行')->options(function ($id) {
                $bank = DB::table('banks')->where('is_del', 1)->select('abbreviation','name')->get();

                $array = [];
                if ($bank) {
                    foreach ($bank as $k => $v) {
                        $array[$v->abbreviation] = $v->name;
                    }
                }
                return $array;
            });
            $form->select('type', '信用卡分类')->options(function ($id) {
                $type = DB::table('credit_types')->where('is_del', 1)->select('id','name')->get();

                $array = [];
                if ($type) {
                    foreach ($type as $k => $v) {
                        $array[$v->id] = $v->name;
                    }
                }
                return $array;
            });
            $form->text('name', '信用卡名称')->rules('required');
            $form->editor('content', '信用卡详情')->rules('required');
            $form->number('sort', '排序')->default('99');
            $form->image('picture', '信用卡图片')->rules('required|image');
            $form->radio('is_hot', '是否热卡')->options(['1' => '是', '0'=> '否'])->default('0');
            $form->radio('is_new', '是否最新')->options(['1' => '是', '0'=> '否'])->default('0');
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
