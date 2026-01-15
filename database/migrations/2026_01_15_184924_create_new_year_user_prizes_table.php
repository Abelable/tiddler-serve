<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewYearUserPrizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_year_user_prizes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->comment('用户id');

            $table->unsignedBigInteger('prize_id')->index()->comment('奖品id');
            $table->tinyInteger('prize_type')->comment('奖品类型：1-福气值，2-优惠券，3-商品');
            $table->tinyInteger('status')->default(0)->comment('奖品状态：0-未使用，1-已使用');

            $table->string('cover', 500)->comment('奖品图片');
            $table->string('name', 100)->comment('奖品名称');
            $table->unsignedBigInteger('coupon_id')->default(0)->comment('优惠券id');
            $table->unsignedBigInteger('goods_id')->default(0)->comment('商品id');

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
        Schema::dropIfExists('new_year_user_prizes');
    }
}
