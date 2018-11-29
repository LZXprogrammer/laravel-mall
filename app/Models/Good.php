<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Good extends Model
{
    public $timestamps = false;

    public function getApplyCityAttribute($value)
    {
        return explode(',', $value);
    }

    public function setApplyCityAttribute($value)
    {
        $this->attributes['apply_city'] = implode(',', $value);
    }

    public function getDetailsPicAttribute($value)
    {
        return explode(',', $value);
    }

    public function setDetailsPicAttribute($value)
    {
        $this->attributes['details_pic'] = implode(',', $value);
    }
    /**
     * 获取查询字段
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'vender', 'category', 'serial_number', 'price', 'details', 'total', 'sold_out', 'courier_fees',
        'create_time', 'apply_city', 'is_del', 'is_show', 'desc', 'show_pic', 'details_pic'
    ];

    // 表示 create_time 是一个日期字段
    //protected $dates = ['create_time'];

    /**
     * 隐藏字段
     *
     * @var array
     */
    protected $hidden = [];

    public function sku()
    {
        return $this->hasMany(GoodSku::class, 'g_id');
    }
}