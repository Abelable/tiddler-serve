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
            $table->tinyInteger('status')->default(0)
                ->comment('奖品状态：0-未使用/未领取，1-已使用/已领取，2-已发货, 3-确认收货');

            $table->string('cover', 500)->comment('奖品图片');
            $table->string('name', 100)->comment('奖品名称');
            $table->unsignedBigInteger('coupon_id')->default(0)->comment('优惠券id');
            $table->unsignedBigInteger('goods_id')->default(0)->comment('商品id');

            $table->string('consignee', 50)->nullable()->comment('收件人姓名');
            $table->string('mobile', 20)->nullable()->comment('收件人手机号');
            $table->string('address', 255)->nullable()->comment('具体收货地址');

            $table->string('ship_channel', 50)->nullable()->comment('快递公司名称');
            $table->string('ship_code', 20)->nullable()->comment('快递公司编号');
            $table->string('ship_sn', 50)->nullable()->comment('快递单号');
            $table->dateTime('ship_time')->nullable()->comment('发货时间');
            $table->dateTime('confirm_time')->nullable()->comment('确认收货时间');

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
