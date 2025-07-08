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
            $table->integer('status')->default(0)->comment('状态：0-未支付保证金，1-已支付保证金');
            $table->integer('user_id')->comment('用户id');
            $table->integer('merchant_id')->comment('商家id');
            $table->integer('type')->comment('店铺类型：1-景区官方，2-旅行社，3-平台自营');
            $table->float('deposit')->comment('店铺保证金');
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
        Schema::dropIfExists('scenic_shops');
    }
}
