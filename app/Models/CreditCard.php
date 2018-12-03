<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    public $timestamps = false;
    /**
     * 获取查询字段
     *
     * @var array
     */
    protected $fillable = [
        'id', 'bank_name', 'type', 'name', 'content', 'create_time', 'sort', 'picture', 'blurb', 'apply_num'
    ];

    // 表示 create_time 是一个日期字段
    protected $dates = [];

    /**
     * 隐藏字段
     *
     * @var array
     */
    protected $hidden = [];


    public function credit_type()
    {
        return $this->belongsTo(CreditType::class, 'type', 'id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_name', 'abbreviation');
    }
}