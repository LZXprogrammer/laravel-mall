<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportDistributionRecord extends Model
{
    public $timestamps = false;
    /**
     * 获取查询字段
     *
     * @var array
     */
    protected $fillable = [
        'id', 'order_id', 'agency_uid', 'agency_amount', 'level', 'create_time'
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