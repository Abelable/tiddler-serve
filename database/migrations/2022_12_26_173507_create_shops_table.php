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
            $table->string('name')->comment('店铺名称');
            $table->string('category_ids')->comment('店铺分类id');
            $table->integer('type')->comment('店铺类型：1-个人，2-企业');
            $table->string('cover')->default('')->comment('店铺封面图片');
            $table->string('logo')->comment('店铺logo');
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
