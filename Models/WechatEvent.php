<?php

namespace Kwin\WechatAdmin\Models;

use Illuminate\Database\Eloquent\Model;

class WechatEvent extends Model
{
    const EVENTLIST = [
        1 => '点击事件',
        2 => '关注事件',
        3 => '取消关注'
    ];
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
    protected $table = "wechat_event";

    public function message()
    {
        return $this->belongsTo(WechatMessage::class, 'wechat_message_id', 'id');
    }
}
