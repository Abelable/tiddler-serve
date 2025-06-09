<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id')->comment('酒店分类id');
            $table->string('name')->comment('酒店名称');
            $table->string('english_name')->comment('酒店英文名称');
            $table->integer('grade')->comment('酒店等级：1-经济，2-舒适，3-高档，4-豪华');
            $table->float('price')->comment('酒店最低价格');
            $table->string('video')->default('')->comment('视频');
            $table->string('cover')->comment('封面图片');
            $table->longText('appearance_image_list')->comment('外观图片列表');
            $table->longText('interior_image_list')->comment('内景图片列表');
            $table->longText('room_image_list')->comment('房间图片列表');
            $table->longText('environment_image_list')->comment('环境图片列表');
            $table->longText('restaurant_image_list')->comment('餐厅图片列表');
            $table->decimal('longitude', 9, 6)->comment('经度');
            $table->decimal('latitude', 8, 6)->comment('纬度');
            $table->string('address')->comment('具体地址');
            $table->string('feature_tag_list')->default('')->comment('酒店特点');
            $table->string('opening_year')->comment('开业年份');
            $table->string('last_decoration_year')->default('')->comment('最近一次装修年份');
            $table->integer('room_num')->comment('房间数量');
            $table->string('tel')->comment('酒店联系电话');
            $table->string('brief')->default('')->comment('简介');
            $table->longText('recreation_facility')->comment('娱乐设施');
            $table->longText('health_facility')->comment('康体设施');
            $table->longText('children_facility')->comment('儿童设施');
            $table->longText('common_facility')->comment('通用设施');
            $table->longText('public_area_facility')->comment('公共区设施');
            $table->longText('traffic_service')->comment('交通服务');
            $table->longText('catering_service')->comment('餐饮服务');
            $table->longText('reception_service')->comment('前台服务');
            $table->longText('clean_service')->comment('清洁服务');
            $table->longText('business_service')->comment('商务服务');
            $table->longText('other_service')->comment('其他服务');
            $table->longText('remind_list')->comment('酒店政策-重要提醒');
            $table->longText('check_in_tip_list')->comment('酒店政策-入住必读');
            $table->longText('preorder_tip_list')->comment('酒店政策-预定须知');
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
        Schema::dropIfExists('hotels');
    }
}
