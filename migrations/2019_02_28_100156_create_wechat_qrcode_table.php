<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatQrcodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_qrcode', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wechat_qrcode_cate_id', false, true)->nullable()->comment('二维码分组ID');
            $table->tinyInteger('qrcode_type')->default('1')->comment('二维码类型 1临时 2永久');
            $table->string('scene_id', 50)->comment('场景值ID');
            $table->string('ticket', 150);
            $table->timestamp('expire_time')->nullable()->default(null)->comment("过期时间");
            $table->string('url', 255)->comment('解析后的地址');
            $table->string('img_url', 255)->comment('二维码图片的地址');
            $table->integer('sweep_number')->default('0')->comment('扫码人数');
            $table->tinyInteger('exported')->default(0)->comment('是否已经导出');
            $table->timestamps();
            $table->foreign('wechat_qrcode_cate_id')->references('id')->on('wechat_qrcode_cate')->onDelete('cascade');
            $table->index('ticket');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wechat_qrcode');
    }
}
