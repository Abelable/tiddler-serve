<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScenicShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scenic_shops', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->default(0)->comment('状态：0-未支付保证金，1-已支付保证金');
            $table->unsignedBigInteger('user_id')->index()->comment('用户ID');
            $table->unsignedBigInteger('merchant_id')->index()->comment('商家ID');

            $table->tinyInteger('type')->comment('店铺类型：1-景区官方，2-旅行社，3-平台自营');
            $table->unsignedDecimal('deposit', 10, 2)->default(0)->comment('店铺初始保证金');

            $table->string('bg', 255)->default('')->comment('店铺背景图');
            $table->string('logo', 255)->default('')->comment('店铺logo');
            $table->string('name', 100)->index()->comment('店铺名称');

            $table->string('owner_avatar', 255)->default('')->comment('店主头像');
            $table->string('owner_name', 50)->default('')->comment('店主姓名');
            $table->string('mobile', 20)->default('')->index()->comment('联系方式');

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
        Schema::dropIfExists('scenic_shops');
    }
}
