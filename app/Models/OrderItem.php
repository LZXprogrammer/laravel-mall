<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public $timestamps = false;
    /**
     * 获取查询字段
     *
     * @var array
     */
    protected $fillable = [
        'id', 'order_id', 'product_id', 'product_sku_id', 'amount', 'price', 'create_time'
    ];

    // 表示 create_time 是一个日期字段
    protected $dates = [];

    /**
     * 隐藏字段
     *
     * @var array
     */
    protected $hidden = [];

    //关联商品
    public function goods()
    {
        return $this->belongsTo(Good::class, 'product_id');
    }

    //关联商品sku
    public function goods_sku()
    {
        return $this->belongsTo(GoodSku::class, 'product_sku_id');
    }

    //关联订单主表
    public function orders()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}