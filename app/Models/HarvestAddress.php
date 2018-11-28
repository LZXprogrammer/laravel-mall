<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HarvestAddress extends Model
{
    public $timestamps = false;
    /**
     * 获取查询字段
     *
     * @var array
     */
    protected $fillable = [
        'id', 'c_id', 'province_id', 'city_id', 'area_id', 'address', 'create_time', 'is_del', 'is_default'
    ];

    // 表示 create_time 是一个日期字段
    protected $dates = ['create_time'];

    /**
     * 隐藏字段
     *
     * @var array
     */
    protected $hidden = [];
}