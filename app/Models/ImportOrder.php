<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportOrder extends Model
{
    public $timestamps = false;
    /**
     * 获取查询字段
     *
     * @var array
     */
    protected $fillable = [
        'id', 'mobile', 'c_id', 'no', 'good_name', 'g_id', 'sku_name', 'sku_id', 'amount', 'trading_time',
        'trading_status', 'create_time'
    ];

    // 表示 create_time 是一个日期字段
    protected $dates = [];

    /**
     * 隐藏字段
     *
     * @var array
     */
    protected $hidden = [];
}