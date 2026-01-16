<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewYearPrizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_year_prizes', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(1)->comment('状态：1-上架中；2-已下架');

            $table->tinyInteger('type')->comment('类型：1-福气值，2-优惠券，3-商品');
            $table->unsignedBigInteger('coupon_id')->default(0)->comment('优惠券id');
            $table->unsignedBigInteger('goods_id')->default(0)->comment('商品id');
            $table->tinyInteger('is_big')->default(0)->comment('是否是大奖：0-否，1-是');

            $table->string('cover', 500)->comment('奖品图片');
            $table->string('name', 100)->comment('奖品名称');

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
        Schema::dropIfExists('new_year_prizes');
    }
}
