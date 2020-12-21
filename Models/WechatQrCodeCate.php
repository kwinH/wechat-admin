<?php

namespace Kwin\WechatAdmin\Models;

use Illuminate\Database\Eloquent\Model;

class WechatQrCodeCate extends Model
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
    protected $table = "wechat_qrcode_cate";


    public function setWechatMessageIdAttribute($value)
    {
        $this->attributes['wechat_message_id'] = empty($value) ? null : $value;
    }

    public function message()
    {
        return $this->belongsTo(WechatMessage::class, 'wechat_message_id', 'id');
    }
}
