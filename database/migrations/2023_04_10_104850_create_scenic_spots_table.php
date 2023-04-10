<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScenicSpotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scenic_spots', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(1)->comment('状态（用户编辑提交的景区）：0-待审核，1-审核通过');
            $table->integer('category_id')->comment('景区分类id');
            $table->string('name')->comment('景区名称');
            $table->float('longitude')->default(0)->comment('经度');
            $table->float('latitude')->default(0)->comment('纬度');
            $table->string('address')->default('')->comment('具体地址');
            $table->float('rate')->default(0)->comment('景区评分');
            $table->string('video')->default('')->comment('视频');
            $table->longText('image_list')->comment('图片列表');
            $table->longText('brief')->comment('简介');
            $table->string('policy_list')->default('')->comment('优待政策');
            $table->string('hotline_list')->default('')->comment('景区热线');
            $table->longText('facility_list')->default('')->comment('景区设施');
            $table->longText('tips_list')->default('')->comment('游玩贴士');
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
        Schema::dropIfExists('scenic_spots');
    }
}
