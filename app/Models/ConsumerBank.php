<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumerBank extends Model
{
    public $timestamps = false;
    /**
     * 获取查询字段
     *
     * @var array
     */
    protected $fillable = [
        'id', 'c_id', 'bank_name', 'bank_card', 'create_time', 'is_del', 'is_default', 'reserved_mobile'
    ];

    // 表示 create_time 是一个日期字段
    protected $dates = [];

    /**
     * 隐藏字段
     *
     * @var array
     */
    protected $hidden = [];

    public function bank() {
        return $this->belongsTo(Bank::class, 'bank_name', 'abbreviation');
    }
}