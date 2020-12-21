<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_event', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->default('')->comment('事件说明标题');
            $table->string('key', 64)->default('');
            $table->tinyInteger('event')->default(1)->comment('1点击事件 2关注 3取消关注');
            $table->string('method')->default('');
            $table->integer('wechat_message_id', false, true);
            $table->timestamps();

            $table->foreign('wechat_message_id')->references('id')->on('wechat_message')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wechat_event');
    }
}
