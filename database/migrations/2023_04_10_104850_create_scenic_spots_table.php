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
            $table->integer('category_id')->comment('景区分类id');
            $table->string('name')->comment('景区名称');
            $table->string('level')->default('')->comment('景区等级');
            $table->float('price')->comment('景区门票最低价格');
            $table->decimal('longitude', 9, 6)->comment('经度');
            $table->decimal('latitude', 8, 6)->comment('纬度');
            $table->string('address')->comment('具体地址');
            $table->string('video')->default('')->comment('视频');
            $table->longText('image_list')->comment('图片列表');
            $table->longText('brief')->comment('简介');
            $table->longText('open_time_list')->comment('开放时间');
            $table->longText('policy_list')->comment('优待政策');
            $table->longText('hotline_list')->comment('景区热线');
            $table->longText('project_list')->comment('景区项目');
            $table->longText('facility_list')->comment('景区设施');
            $table->longText('tips_list')->comment('游玩贴士');
            $table->string('feature_tag_list')->default('')->comment('景区特色标签');
            $table->integer('sales_volume')->default(0)->comment('销量');
            $table->float('score')->default(0)->comment('评分');
            $table->integer('views')->default(0)->comment('点击率');
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
