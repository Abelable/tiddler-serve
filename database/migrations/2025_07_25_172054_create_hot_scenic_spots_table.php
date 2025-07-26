<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotScenicSpotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hot_scenic_spots', function (Blueprint $table) {
            $table->id();
            $table->integer('scenic_id')->comment('景点id');
            $table->string('scenic_cover')->comment('景点封面');
            $table->string('scenic_name')->comment('景点名称');
            $table->string('recommend_reason')->comment('推荐理由');
            $table->integer('interested_user_number')->comment('感兴趣人数');
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
        Schema::dropIfExists('hot_scenic_spots');
    }
}
