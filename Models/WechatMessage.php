<?php

namespace Kwin\WechatAdmin\Models;

use Illuminate\Database\Eloquent\Model;

class WechatMessage extends Model
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
    protected $table = "wechat_message";


    public function news_item()
    {
        return $this->hasMany(WechatNewsItem::class);
    }
}
