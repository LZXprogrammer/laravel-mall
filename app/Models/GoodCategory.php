<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodCategory extends Model
{
    public $timestamps = false;
    /**
     * 获取查询字段
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'create_time', 'is_del'
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