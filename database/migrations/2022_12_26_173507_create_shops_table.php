<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(0)->comment('状态：0-未支付保证金，1-已支付保证金');
            $table->integer('user_id')->comment('用户id');
            $table->integer('merchant_id')->comment('商家id');
            $table->integer('type')->comment('店铺类型：1-个人，2-企业');
            $table->float('deposit')->comment('店铺保证金');
            $table->string('category_ids')->comment('店铺分类id');
            $table->string('bg')->default('')->comment('店铺背景图');
            $table->string('logo')->comment('店铺logo');
            $table->string('name')->comment('店铺名称');
            $table->string('brief')->default('')->comment('店铺简称');
            $table->string('owner_avatar')->default('')->comment('店主头像');
            $table->string('owner_name')->default('')->comment('店主姓名');
            $table->string('mobile')->default('')->comment('联系方式');
            $table->string('address_detail')->default('')->comment('店铺地址详情');
            $table->decimal('longitude', 9, 6)->default(0)->comment('店铺经度');
            $table->decimal('latitude', 8, 6)->default(0)->comment('店铺纬度');
            $table->string('open_time_list')->default('[]')->comment('店铺营业时间');
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
        Schema::dropIfExists('shops');
    }
}
