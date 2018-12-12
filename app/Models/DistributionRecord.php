<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributionRecord extends Model
{
    public $timestamps = false;

    /**
     * 获取查询字段
     *
     * @var array
     */
    protected $fillable = [
        'id', 'order_id', 'primary_agency', 'primary_agency_amount', 'secondary_agency', 'secondary_agency_amount', 'three_agency',
        'three_agency_amount', 'create_time'
    ];


    /**
     * 隐藏字段
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * 
     */
    public function consumer()
    {
        return $this->belongsTo(Consumer::class, 'agency_uid');
    }
}