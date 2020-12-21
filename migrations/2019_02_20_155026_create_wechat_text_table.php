<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatTextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_text', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key');
            $table->tinyInteger('type')->default('1')->comment('1 全匹配 2半匹配 3正则匹配');
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
        Schema::dropIfExists('wechat_text');
    }
}
