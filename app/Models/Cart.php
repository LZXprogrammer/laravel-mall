<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    public $timestamps = false;
    /**
     * 获取查询字段
     *
     * @var array
     */
    protected $fillable = [
        'id', 'c_id', 'g_sku_id', 'amount', 'create_time'
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
        return $this->belongsTo(Consumer::class, 'c_id');
    }

    /**
     * 模型关联 商品
     */
    public function goodsku()
    {
        return $this->belongsTo(GoodSku::class, 'g_sku_id');
    }
}