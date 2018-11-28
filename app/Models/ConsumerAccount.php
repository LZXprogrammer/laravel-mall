<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumerAccount extends Model
{
    public $timestamps = false;
    /**
     * 获取查询字段
     *
     * @var array
     */
    protected $fillable = [
        'id',
    ];

    // 表示 create_time 是一个日期字段
    protected $dates = ['create_time', 'real_time', 'active_time'];

    /**
     * 隐藏字段
     *
     * @var array
     */
    protected $hidden = [];


}