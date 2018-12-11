<?php

namespace App\Admin\Controllers;

use App\Models\Good;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\DB;

class GoodsController extends Controller
{
    use HasResourceActions;

    //商品首页
    public function index(Content $content)
    {
        return Admin::content(function (Content $content) {
            $content->header('商品列表');
            $content->body($this->grid());
        });
    }

    //编辑商品
    public function edit($id, Content $content)
    {
        return $content->header('商品信息')->description('编辑')->body($this->form()->edit($id));
    }

    //编辑商品
    public function update($id)
    {
        return $this->form()->update($id);
    }

    //查看商品详情
    public function show($id, Content $content)
    {
        return $content
            ->header('商品信息')
            ->description('详情')
            ->body($this->detail($id));
    }

    //添加商品
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header('创建商品');
            $content->body($this->form());
        });
    }

    //添加商品保存回调
    public function store()
    {
        return $this->form()->store();
    }

    //获取商品数据
    protected function grid()
    {
        return Admin::grid(Good::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->name('产品名称');
            $grid->vender('厂家');
            $grid->category('产品类别')->display(function ($value) {
                return DB::table('good_categories')->where('id', $value)->value('name');
            });
            $grid->serial_number('产品序列号');
            $grid->price('产品价格');
            $grid->create_time('创建时间')->display(function ($value) {
                return date("Y-m-d H:i:s", $value);
            });
            $grid->courier_fees('快递费');
            $grid->is_show('是否显示')->display(function ($value) {
                return ($value==1) ? '是' : '否';
            });
            $grid->desc('排序')->sortable();

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
        return Admin::form(Good::class, function (Form $form) {
            $form->text('name', '产品名称')->rules('required');
            $form->text('vender', '厂家')->rules('required');
            $form->select('category','产品类别')->options(function ($name) {
                $bank = DB::table('good_categories')->where('is_del', 1)->select('id','name')->get();
                $array = [];
                if ($bank) {
                    foreach ($bank as $k => $v) {
                        $array[$v->id] = $v->name;
                    }
                }
                return $array;
            });
            $form->text('serial_number', '产品序列号')->rules('required');
            $form->currency('price', '价格')->symbol('￥')->rules('required');
            $form->editor('details', '产品详情')->rules('required');
            $form->number('total', '总数量')->placeholder('不填默认为产品数量无限制');
            $form->currency('courier_fees', '快递费')->symbol('￥')->rules('required');
            $form->multipleSelect('apply_city','适用城市')->options(function ($name) {
                $bank = DB::table('areas')->where('level', 2)->select('ad_code','name')->get();
                $array['-1'] = '全部城市';
                if ($bank) {
                    foreach ($bank as $k => $v) {
                        $array[$v->ad_code] = $v->name;
                    }
                }
                return $array;
            });
            $form->radio('is_show', '是否显示')->options(['1' => '是', '0'=> '否'])->default('0');
            $form->number('sort', '排序')->placeholder('数值越大越靠前');
            //单图上传
            $form->image('show_pic', '展示图片')->rules('required|image');
            $form->hidden('create_time')->value(time());
            //多图上传 添加删除按钮
            $form->multipleImage('details_pic', '产品详情图片')->removable();
            //添加商品通道
            $form->hasMany('sku', '交易通道', function (Form\NestedForm $form) {
                $form->text('trad_channel', '交易通道')->rules('required');
                $form->currency('market_a', '一级分销代理人金额')->symbol('￥')->rules('required');
                $form->currency('market_b', '二级分销代理人金额')->symbol('￥')->rules('required');
                $form->currency('market_c', '三级分销代理人金额')->symbol('￥')->rules('required');
                $form->currency('extra', '额外结算金额')->symbol('￥')->rules('required');
                $form->hidden('create_time')->value(time());
            });

            // 定义事件回调，当模型即将保存时会触发这个回调
            $form->saving(function (Form $form) {
                foreach ($form->apply_city as $k => $v) {
                    if($v == '-1') {
                        $form->apply_city = ['0' => '-1'];
                    }
                }
            });
            $form->tools(function (Form\Tools $tools) {
                // 去掉`删除`按钮
                $tools->disableDelete();
            });
        });
    }

    /**
     * 商品详情
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Good::findOrFail($id));

        $show->name('产品名称');
        $show->vender('厂家');
        $show->category('产品类别')->as(function ($info) {
            return DB::table('good_categories')->where('id', $info)->value('name');
        });
        $show->serial_number('产品序列号');
        $show->price('产品价格');
        $show->details('产品详情')->link();
        $show->total('总数量');
        $show->sold_out('已售数量');
        $show->courier_fees('快递费');
        $show->create_time('创建时间')->as(function ($info) {
                return date('Y-d-m H:i:s', $info);
        });
        $show->apply_city('适用城市')->as(function ($info) {
             $city = DB::table('areas')->whereIn('id', $info)->select('name')->get();
             if($city) {
                 $apply_city = '';
                 foreach ($city as $k => $v) {
                     if($v->name == '-1') {
                         return '全部城市';
                     }else{
                         $apply_city .= $v->name.',';
                     }
                 }
                 return rtrim($apply_city, ',');
             }
        });

        $show->is_show('是否显示')->using(['0' => '否', '1' => '是']);
        $show->desc('排序');
        $show->show_pic('展示图片')->image();
        $show->details_pic('详情图片')->images();
        $show->panel()->tools(function ($tools) {
                $tools->disableDelete();
            });
        $show->sku('商品通道信息', function ($sku) {
            $sku->setResource('/admin/good');
            $sku->trad_channel('交易通道');
            $sku->market_a('一级分销代理人金额');
            $sku->market_b('二级分销代理人金额');
            $sku->market_c('三级分销代理人金额');
            $sku->extra('额外结算金额');
            $sku->disableCreateButton();
            $sku->disableExport();
            $sku->actions(function ($actions) {
                $actions->disableView();
                $actions->disableDelete();
                $actions->disableEdit();
            });
            $sku->tools(function ($tools) {
                // 禁用批量删除按钮
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
        });
        return $show;
    }
}
