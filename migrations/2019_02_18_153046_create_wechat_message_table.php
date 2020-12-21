<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_message', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('msg_type', ['text', 'image', 'voice', 'video', 'news']);
            $table->string('media_id')->default('')->comment('通过素材管理中的接口上传多媒体文件，得到的id');
            $table->string('title')->default('')->comment('消息的标题');
            $table->text('description')->nullable()->comment('消息的描述');
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
        Schema::dropIfExists('wechat_message');
    }
}
