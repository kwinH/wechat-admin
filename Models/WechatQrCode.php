<?php

namespace Kwin\WechatAdmin\Models;

use Illuminate\Database\Eloquent\Model;

class WechatQrCode extends Model
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
    protected $table = "wechat_qrcode";


    public static $typeList = [
        1 => '临时',
        2 => '永久'
    ];
    

    public function cate()
    {
        return $this->belongsTo(WechatQrCodeCate::class, 'wechat_qrcode_cate_id', 'id');
    }
}
