<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNightTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('night_trips', function (Blueprint $table) {
            $table->id();
            $table->integer('scenic_id')->comment('景点id');
            $table->string('scenic_cover')->comment('景点封面');
            $table->string('scenic_name')->comment('景点名称');
            $table->string('feature_tips')->comment('特色');
            $table->string('recommend_tips')->comment('推荐');
            $table->string('guide_tips')->comment('特色');
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
        Schema::dropIfExists('night_trips');
    }
}
