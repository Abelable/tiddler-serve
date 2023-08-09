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
            $table->integer('status')->default(0)->comment('申请状态：0-待审核，1-审核通过，2-审核未通过');
            $table->string('failure_reason')->default('')->comment('审核失败原因');
            $table->integer('category_id')->comment('酒店分类id');
            $table->string('name')->comment('酒店名称');
            $table->integer('grade')->comment('酒店等级：1-经济，2-舒适，3-高档，4-豪华');
            $table->float('price')->comment('酒店最低价格');
            $table->float('longitude')->comment('经度');
            $table->float('latitude')->comment('纬度');
            $table->string('address')->comment('具体地址');
            $table->float('rate')->default(0)->comment('酒店评分');
            $table->string('video')->default('')->comment('视频');
            $table->longText('image_list')->comment('图片列表');
            $table->string('feature_tag_list')->default('')->comment('酒店特点');
            $table->string('opening_year')->comment('开业年份');
            $table->string('last_decoration_year')->default('')->comment('最近一次装修年份');
            $table->integer('room_num')->comment('房间数量');
            $table->string('tel')->comment('酒店联系电话');
            $table->string('brief')->default('')->comment('简介');
            $table->longText('facility_list')->comment('酒店设施');
            $table->longText('service_list')->comment('酒店服务');
            $table->longText('remind_list')->comment('酒店政策-重要提醒');
            $table->longText('check_in_tip_list')->comment('酒店政策-入住必读');
            $table->longText('preorder_tip_list')->comment('酒店政策-预定须知');
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
