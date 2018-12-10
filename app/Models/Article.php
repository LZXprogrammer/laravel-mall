<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    public $timestamps = false;

    // 表示 show_time, end_time 是一个日期字段
    protected $dates = ['show_time', 'end_time'];

    /**
     * 获取查询字段
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'blurb', 'picture', 'type', 'url', 'content', 'show_time', 'end_time', 'is_show', 'is_del'
    ];


}