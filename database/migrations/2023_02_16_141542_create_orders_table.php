<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // 基础信息
            $table->string('order_sn', 64)->unique()->comment('订单编号');
            $table->unsignedSmallInteger('status')->index()->comment('订单状态');

            // 用户相关
            $table->unsignedBigInteger('user_id')->index()->comment('用户ID');

            // 配送信息
            $table->tinyInteger('delivery_mode')->default(1)->comment('配送方式：1-快递，2-自提');
            $table->string('consignee', 50)->default('')->comment('收件人姓名');
            $table->string('mobile', 20)->default('')->comment('收件人手机号');
            $table->string('address', 255)->default('')->comment('具体收货地址');

            // 自提信息
            $table->unsignedBigInteger('pickup_address_id')->default(0)->comment('提货地址id');
            $table->dateTime('pickup_time')->nullable()->comment('提货时间');
            $table->string('pickup_mobile', 20)->default('')->comment('提货预留手机号');

            // 店铺信息（冗余）
            $table->unsignedBigInteger('shop_id')->default(0)->index()->comment('店铺id');
            $table->string('shop_logo', 255)->default('')->comment('店铺logo');
            $table->string('shop_name', 100)->default('')->comment('店铺名称');

            // 金额信息（全部 unsignedDecimal）
            $table->unsignedDecimal('goods_price', 10, 2)->default(0)->comment('商品总价格');
            $table->unsignedDecimal('freight_price', 10, 2)->default(0)->comment('运费');
            $table->unsignedBigInteger('coupon_id')->default(0)->comment('优惠券ID');
            $table->unsignedBigInteger('coupon_shop_id')->default(0)->comment('优惠券店铺ID');
            $table->unsignedDecimal('coupon_denomination', 10, 2)->default(0)->comment('优惠券抵扣金额');
            $table->unsignedDecimal('deduction_balance', 10, 2)->default(0)->comment('余额抵扣金额');

            $table->unsignedDecimal('payment_amount', 10, 2)->default(0)->comment('实际支付金额');
            $table->unsignedDecimal('total_payment_amount', 10, 2)->default(0)->comment('订单总支付金额(拆单场景)');

            // 支付信息
            $table->string('pay_id', 64)->default('')->comment('支付ID');
            $table->dateTime('pay_time')->nullable()->comment('支付时间');

            // 发货/完成信息
            $table->dateTime('ship_time')->nullable()->comment('发货时间');
            $table->dateTime('confirm_time')->nullable()->comment('用户确认收货时间');
            $table->dateTime('finish_time')->nullable()->comment('订单关闭/完成时间');

            // 退款信息
            $table->unsignedDecimal('refund_amount', 10, 2)->default(0)->comment('退款金额');
            $table->string('refund_id', 64)->default('')->comment('微信退款ID');
            $table->tinyInteger('refund_type')->default(0)->comment('退款方式：0-无，1-部分，2-全部');
            $table->string('refund_remarks', 255)->default('')->comment('退款备注');
            $table->dateTime('refund_time')->nullable()->comment('退款时间');

            // 备注
            $table->string('remarks', 255)->default('')->comment('订单备注');

            $table->timestamps();
            $table->softDeletes();

            // 索引优化
            $table->index(['shop_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
