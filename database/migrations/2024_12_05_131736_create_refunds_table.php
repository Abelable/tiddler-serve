<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();

            $table->tinyInteger('status')
                ->default(0)
                ->comment('申请状态：0-待审核，1-审核通过，等待买家寄回，2-买家已寄出，待确认，3-退款成功，4-审核失败');
            $table->string('failure_reason', 255)->nullable()->comment('审核失败原因');

            $table->unsignedBigInteger('user_id')->index()->comment('用户id');
            $table->unsignedBigInteger('shop_id')->default(0)->index()->comment('店铺id');
            $table->unsignedBigInteger('order_id')->index()->comment('订单id');
            $table->string('order_sn', 50)->index()->comment('订单编号');
            $table->unsignedBigInteger('order_goods_id')->index()->comment('订单商品id');
            $table->unsignedBigInteger('goods_id')->index()->comment('商品id');
            $table->unsignedBigInteger('coupon_id')->nullable()->comment('优惠券id');

            $table->unsignedDecimal('refund_amount', 10, 2)->default(0)->comment('退款金额');

            $table->tinyInteger('refund_type')->comment('售后类型：1-仅退款，2-退货退款');
            $table->string('refund_reason', 255)->nullable()->comment('退款说明');
            $table->json('image_list')->nullable()->comment('图片说明');

            $table->unsignedBigInteger('refund_address_id')->nullable()->comment('退货地址id');

            $table->string('ship_channel', 50)->nullable()->comment('退货快递公司');
            $table->string('ship_code', 20)->nullable()->comment('快递公司编码');
            $table->string('ship_sn', 50)->nullable()->comment('退货快递单号');

            $table->unsignedBigInteger('reviewer_id')->nullable()->comment('审核管理员ID');
            $table->dateTime('reviewed_at')->nullable()->comment('审核时间');
            $table->dateTime('refunded_at')->nullable()->comment('退款成功时间');

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
        Schema::dropIfExists('refunds');
    }
}
