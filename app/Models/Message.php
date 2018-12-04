<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public $timestamps = false;
    /**
     * 获取查询字段
     *
     * @var array
     */
    protected $fillable = [
        'id', 'mobile', 'message_template_id', 'code', 'message', 'send_time', 'overdue_time', 'is_use', 'type'
    ];

    // 表示 create_time 是一个日期字段
    protected $dates = [];

    /**
     * 隐藏字段
     *
     * @var array
     */
    protected $hidden = [];


    public function message_template()
    {
        return $this->belongsTo(MessageTemplate::class);
    }
}