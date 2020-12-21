<?php

namespace Kwin\WechatAdmin\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;

class WxMembers extends Model
{
    use DefaultDatetimeFormat;

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
    protected $table = "wx_members";


    const SUBSCRIBE_SCENE = [
        'ADD_SCENE_SEARCH' => '公众号搜索',
        'ADD_SCENE_ACCOUNT_MIGRATION' => '公众号迁移',
        'ADD_SCENE_PROFILE_CARD' => '名片分享',
        'ADD_SCENE_QR_CODE' => '扫描二维码',
        'ADD_SCENE_PROFILE_LINK' => '图文页内名称点击',
        'ADD_SCENE_PROFILE_ITEM' => '图文页右上角菜单',
        'ADD_SCENE_PAID' => '支付后关注',
        'ADD_SCENE_WECHAT_ADVERTISEMENT' => '微信广告',
        'ADD_SCENE_OTHERS' => '其他'
    ];

    const SEX_LIST = [
        0 => '未知',
        1 => '男',
        2 => '女'
    ];
}
