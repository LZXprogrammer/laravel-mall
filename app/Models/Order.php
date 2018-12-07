<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public $timestamps = false;
    /**
     * 获取查询字段
     *
     * @var array
     */
    protected $fillable = [
        'id', 'no', 'c_id', 'address', 'total_amount', 'remark', 'paid_time', 'payment_method', 'payment_no', 'pay_status',
        'refund_status', 'refund_no', 'closed', 'reviewed', 'ship_status', 'ship_data', 'extra', 'create_time'
    ];

    // 表示 create_time 是一个日期字段
    protected $dates = [];

    /**
     * 隐藏字段
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * 模型关联 用户
     */
    public function consumer()
    {
        return $this->belongsTo(Consumer::class);
    }

    /**
     * 模型关联 订单子表
     */
    public function orderitems()
    {
        return $this->hasMany(OrderItem::class);
    }
}