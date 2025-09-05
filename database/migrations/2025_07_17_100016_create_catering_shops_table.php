<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering_shops', function (Blueprint $table) {
            $table->id();
            $table->integer('status')
                ->default(0)
                ->comment('状态：0-未支付保证金，1-已支付保证金');
            $table->integer('user_id')->comment('用户id');
            $table->integer('merchant_id')->comment('服务商id');
            $table->integer('type')->comment('店铺类型：1-餐饮官方，2-专营店，3-平台自营');
            $table->float('deposit')->comment('店铺保证金');
            $table->string('owner_avatar')->default('')->comment('店主头像');
            $table->string('owner_name')->default('')->comment('店主姓名');
            $table->string('mobile')->default('')->comment('联系方式');
            $table->string('bg')->default('')->comment('店铺背景图');
            $table->string('logo')->comment('店铺logo');
            $table->string('name')->comment('店铺名称');
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
        Schema::dropIfExists('catering_shops');
    }
}
