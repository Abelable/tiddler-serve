<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->default(0)->index()->comment('店铺id');
            $table->tinyInteger('status')->default(1)->comment('优惠券状态：1-有效，2-过期，3-下架');

            $table->string('name', 100)->comment('优惠券名称');
            $table->string('description', 255)->nullable()->comment('优惠券说明');

            $table->tinyInteger('type')->default(1)->comment('优惠券类型：1-无门槛，2-商品数量满减，3-价格满减');

            $table->unsignedDecimal('denomination', 10, 2)->default(0)->comment('优惠券面额');
            $table->unsignedInteger('num_limit')->default(0)->comment('优惠券商品数量门槛');
            $table->unsignedDecimal('price_limit', 10, 2)->default(0)->comment('优惠券价格门槛');

            $table->dateTime('expiration_time')->nullable()->comment('优惠券失效时间');

            $table->unsignedInteger('receive_limit')->default(0)->comment('优惠券领取数量限制');
            $table->unsignedInteger('received_num')->default(0)->comment('优惠券领取数量');

            $table->unsignedBigInteger('goods_id')->nullable()->index()->comment('商品id');
            $table->string('goods_cover', 255)->nullable()->comment('商品图片');
            $table->string('goods_name', 255)->nullable()->comment('商品名称');

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
        Schema::dropIfExists('coupons');
    }
}
