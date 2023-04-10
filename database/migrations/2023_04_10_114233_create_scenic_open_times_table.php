<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScenicOpenTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scenic_open_times', function (Blueprint $table) {
            $table->id();
            $table->integer('scenic_id')->comment('景点id');
            $table->string('open_date')->comment('开园日期');
            $table->string('close_date')->comment('闭园日期');
            $table->string('open_time')->comment('开园时间');
            $table->string('close_time')->comment('闭园时间');
            $table->string('tips')->default('')->comment('时间补充说明');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scenic_open_times');
    }
}
