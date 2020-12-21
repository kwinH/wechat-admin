<?php

namespace Kwin\WechatAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Kwin\WechatAdmin\Models\WxMembers;

class WechatQrCodeScanLog extends Model
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
    protected $table = "wechat_qrcode_scan_log";

    public function wxMembers()
    {
        return $this->belongsTo(WxMembers::class, 'user_id');
    }

    public function cate()
    {
        return $this->belongsTo(WechatQrCodeCate::class, 'wechat_qrcode_cate_id', 'id');
    }
}
