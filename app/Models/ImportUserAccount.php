<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportUserAccount extends Model
{
    public $timestamps = false;
    /**
     * 获取查询字段
     *
     * @var array
     */
    protected $fillable = [
        'id', 'c_id', 'total', 'available', 'freeze', 'withdraw', 'market', 'market_a', 'market_b', 'market_c'
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