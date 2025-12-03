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
            $table->tinyInteger('status')->default(0)->comment('状态：0-未支付保证金，1-已支付保证金');
            $table->unsignedBigInteger('user_id')->index()->comment('用户ID');
            $table->unsignedBigInteger('merchant_id')->index()->comment('商家ID');
            $table->tinyInteger('type')->default(1)->comment('店铺类型：1-个人，2-企业');
            $table->unsignedDecimal('deposit', 10, 2)->default(0)->comment('店铺初始保证金');
            $table->json('category_ids')->nullable()->comment('店铺分类ID');
            $table->string('bg', 255)->default('')->comment('店铺背景图');
            $table->string('logo', 255)->default('')->comment('店铺logo');
            $table->string('name', 100)->index()->comment('店铺名称');
            $table->string('brief', 100)->default('')->comment('店铺简称');
            $table->string('owner_avatar', 255)->default('')->comment('店主头像');
            $table->string('owner_name', 50)->default('')->comment('店主姓名');
            $table->string('mobile', 20)->default('')->index()->comment('联系方式');
            $table->string('address_detail', 255)->default('')->comment('店铺地址详情');
            $table->decimal('longitude', 9, 6)->default(0)->index()->comment('经度');
            $table->decimal('latitude', 8, 6)->default(0)->index()->comment('纬度');
            $table->json('open_time_list')->nullable()->comment('店铺营业时间');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['merchant_id', 'status']);
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
