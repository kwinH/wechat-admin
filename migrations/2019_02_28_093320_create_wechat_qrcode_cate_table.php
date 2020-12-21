<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatQrcodeCateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_qrcode_cate', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->comment('分组名称');
            $table->string('desc', 255)->nullable()->comment('分组详情描述');
            $table->integer('sweep_number')->default('0')->comment('扫码人数');
            $table->string('method')->default('')->comment('执行的方法 namespace\\class@method');
            $table->integer('wechat_message_id', false, true)->nullable();
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
        Schema::dropIfExists('wechat_qrcode_cate');
    }
}
