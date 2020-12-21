<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWxMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wx_members', function (Blueprint $table) {
            $table->increments('id');
            $table->string('openid', 255)->default('')->comment('用户的唯一标识');
            $table->string('nickname', 255)->default('')->comment('用户昵称');
            $table->tinyInteger('sex')->default(0)->comment('用户的性别，值为1时是男性，值为2时是女性，值为0时是未知');
            $table->string('province', 30)->default('')->comment('用户个人资料填写的省份');
            $table->string('city', 30)->default('')->comment('用户个人资料填写的城市');
            $table->string('country', 30)->default('')->comment('国家，如中国为CN');
            $table->string('headimgurl', 255)->default('')->comment('用户头像');
            $table->string('remark', 255)->default('')->comment('备注');
            $table->string('language', 30)->default('')->comment('微信用户语言');
            $table->integer('subscribe_time')->default(0)->comment('关注时间');
            $table->string('subscribe_scene', 30)->default('')->comment('关注方式');
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
        Schema::dropIfExists('wx_members');
    }
}
