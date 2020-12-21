<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatNewsItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_news_item', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wechat_message_id', false, true)->nullable();
            $table->string('title')->default('')->comment('标题');
            $table->text('description')->nullable()->comment('描述');
            $table->string('image')->default('')->comment('图片');
            $table->string('url')->default('')->comment('URL');
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
        Schema::dropIfExists('wechat_news_item');
    }
}
