<?php

namespace Kwin\WechatAdmin\Models;

use Illuminate\Database\Eloquent\Model;

class WechatNewsItem extends Model
{
    /**
     * [$guarded description]
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * [$guarded description]
     *
     * @var string
     */
    protected $table = "wechat_news_item";


    public function message()
    {
        return $this->belongsTo(WechatMessage::class, 'wechat_message_id', 'id');
    }
}
