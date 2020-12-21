<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_menu', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->default(0);
            $table->integer('sort')->default(0);
            $table->string('name', 50);
            $table->enum('type', ['','click', 'view', 'scancode_push', 'scancode_waitmsg', 'pic_sysphoto', 'pic_photo_or_album', 'pic_weixin', 'location_select', 'media_id', 'view_limited']);
            $table->string('key', 128)->default('')->comment('click等点击类型必须 菜单KEY值，用于消息接口推送，不超过128字节');
            $table->string('url', 255)->default('')->comment('view、miniprogram类型必须 网页 链接，用户点击菜单可打开链接，不超过1024字节。 type为miniprogram时，不支持小程序的老版本客户端将打开本url。');
            $table->string('media_id', 255)->default('')->comment('miniprogram类型必须 小程序的appid（仅认证公众号可配置）');
            $table->string('appid', 255)->default('')->comment('miniprogram类型必须	小程序的appid（仅认证公众号可配置）');
            $table->string('pagepath', 255)->default('')->comment('miniprogram类型必须	小程序的页面路径');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wechat_menu');
    }
}
