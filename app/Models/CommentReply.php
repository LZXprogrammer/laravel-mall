<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentReply extends Model
{
    public $timestamps = false;
    /**
     * 获取查询字段
     *
     * @var array
     */
    protected $fillable = [
        'id', 'comment_id', 'reply_type', 'reply_id', 'content', 'to_cid', 'from_cid', 
        'from_avatar', 'from_nickname', 'create_time', 'to_nickname', 'is_author'
    ];

    // 表示 create_time 是一个日期字段
    protected $dates = [];

    /**
     * 隐藏字段
     *
     * @var array
     */
    protected $hidden = ['is_top', 'is_hot', 'is_reply'];
}