<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodSku extends Model
{
    public $timestamps = false;

    /**
     * 获取查询字段
     *
     * @var array
     */
    protected $fillable = [
        'id', 'g_id', 'trad_channel', 'market_a', 'market_b', 'market_c', 'extra', 'create_time'
    ];

    // 表示 create_time 是一个日期字段
    //protected $dates = ['create_time'];

    /**
     * 隐藏字段
     *
     * @var array
     */
    protected $hidden = [];

    public function good()
    {
        return $this->belongsTo(Good::class, 'g_id');
    }
}