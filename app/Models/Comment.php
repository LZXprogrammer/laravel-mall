<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public $timestamps = false;
    /**
     * 获取查询字段
     *
     * @var array
     */
    protected $fillable = [
        'id', 'c_id', 'g_id', 'content', 'status', 'reply_num', 'like_num', 'create_time'
    ];

    // 表示 create_time 是一个日期字段
    protected $dates = [];

    /**
     * 隐藏字段
     *
     * @var array
     */
    protected $hidden = ['is_top', 'is_hot', 'is_reply'];

    /**
     * 模型关联 商品
     */
    public function goods()
    {
        return $this->belongsTo(Good::class, 'g_id');
    }

    /**
     * 模型关联 用户
     */
    public function consumer()
    {
        return $this->belongsTo(Consumer::class, 'c_id');
    }

    /**
     * 模型关联 信用卡
     */
    public function credit()
    {
        return $this->belongsTo(CreditCard::class, 'credit_id');
    }

    /**
     * 模型关联 回复评论
     */
    public function comment_replies()
    {
        return $this->hasMany(CommentReply::class, 'comment_id');
    }
}