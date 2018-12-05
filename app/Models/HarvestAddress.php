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
        'id', 'c_id', 'province_id', 'city_id', 'area_id', 'address', 'create_time', 'is_del', 'is_default', 'phone', 'name'
    ];

    // 表示 create_time 是一个日期字段
    protected $dates = [];

    /**
     * 隐藏字段
     *
     * @var array
     */
    protected $hidden = [];

    public function province()
    {
        return $this->belongsTo(Area::class, 'province_id', 'ad_code');
    }

    public function city()
    {
        return $this->belongsTo(Area::class, 'city_id','ad_code');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id','ad_code');
    }

    public function user()
    {
        return $this->belongsTo(Consumer::class, 'c_id');
    }
}