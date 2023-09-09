<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id')->comment('餐馆分类id');
            $table->string('name')->comment('餐馆名称');
            $table->float('price')->comment('餐馆最低价格');
            $table->string('logo')->comment('餐馆头像图片');
            $table->string('video')->default('')->comment('视频');
            $table->string('cover')->comment('餐馆封面图片');
            $table->longText('food_image_list')->comment('菜品图片列表');
            $table->longText('environment_image_list')->comment('环境图片列表');
            $table->longText('price_image_list')->comment('价目表图片列表');
            $table->float('longitude')->comment('经度');
            $table->float('latitude')->comment('纬度');
            $table->string('address')->comment('具体地址');
            $table->float('rate')->default(0)->comment('餐馆综合评分');
            $table->float('taste_rate')->default(0)->comment('餐馆口味评分');
            $table->float('environment_rate')->default(0)->comment('餐馆环境评分');
            $table->float('service_rate')->default(0)->comment('餐馆服务评分');
            $table->float('food_rate')->default(0)->comment('餐馆食材评分');
            $table->longText('tel_list')->comment('餐馆联系电话');
            $table->integer('open_status')->default(0)->comment('营业状态：0-尚未营业，1-正在营业');
            $table->longText('open_time_list')->comment('餐馆营业时间');
            $table->longText('facility_list')->comment('服务设施列表');
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
        Schema::dropIfExists('restaurants');
    }
}
