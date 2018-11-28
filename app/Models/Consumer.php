<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consumer extends Model
{
    public $timestamps = false;
    /**
     * 获取查询字段
     *
     * @var array
     */
    protected $fillable = [
        'id', 'mobile', 'nick_name', 'real_name', 'id_number', 'avatar',
        'is_active', 'level_a', 'level_b', 'level_c', 'create_time', 'real_time', 'active_time'
    ];

    // 表示 create_time 是一个日期字段
    protected $dates = ['create_time', 'real_time', 'active_time'];

    /**
     * 隐藏字段
     *
     * @var array
     */
    protected $hidden = [
        'password', 'rand'
    ];

    //用户资金
    public function account()
    {
        return $this->belongsTo(ConsumerAccount::class, 'id', 'c_id');
    }

    //用户地址列表
    public function address()
    {
        return $this->hasMany(ConsumerAccount::class, 'id', 'c_id');
    }

    //用户银行卡列表
    public function bank()
    {
        return $this->hasMany(ConsumerBank::class,'c_id');
    }
}