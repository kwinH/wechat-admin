<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatQrcodeScanLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_qrcode_scan_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id', false, true)->comment('用户ID');
            $table->integer('wechat_qrcode_id', false, true)->comment('二维码ID');
            $table->integer('wechat_qrcode_cate_id', false, true)->comment('二维码分组ID');
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
        Schema::dropIfExists('wechat_qrcode_scan_log');
    }
}
