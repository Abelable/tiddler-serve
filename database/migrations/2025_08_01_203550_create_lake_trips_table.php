<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLakeTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lake_trips', function (Blueprint $table) {
            $table->id();
            $table->integer('lake_id')->comment('湖区id');
            $table->integer('scenic_id')->comment('景点id');
            $table->string('scenic_cover')->comment('景点封面');
            $table->string('scenic_name')->comment('景点名称');
            $table->string('desc')->comment('描述');
            $table->string('distance')->comment('行程里数（km）');
            $table->string('duration')->comment('行程时长（h）');
            $table->string('time')->comment('最佳时间（月）');
            $table->integer('sort')->default(1)->comment('排序');
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
        Schema::dropIfExists('lake_trips');
    }
}
