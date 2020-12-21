<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visit_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wx_members_id')->default(0);
            $table->string('path', 255)->default('');
            $table->string('method', 10)->default('');
            $table->bigInteger('ip')->default(0);
            $table->text('input');
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
        Schema::dropIfExists('visit_log');
    }
}
