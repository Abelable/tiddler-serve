<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSetMealOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('set_meal_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_sn')->comment('订单编号');
            $table->integer('status')->comment('订单状态');
            $table->integer('user_id')->comment('用户id');
            $table->integer('shop_id')->default(0)->comment('店铺id');
            $table->string('shop_logo')->default('')->comment('店铺头像');
            $table->string('shop_name')->default('')->comment('店铺名称');
            $table->string('consignee')->comment('用户姓名');
            $table->string('mobile')->comment('用户手机号');
            $table->float('total_price')->comment('总价');
            $table->integer('coupon_id')->default(0)->comment('优惠券id');
            $table->float('coupon_denomination')->default(0)->comment('优惠券抵扣金额');
            $table->float('deduction_balance')->default(0)->comment('余额抵扣金额');
            $table->float('payment_amount')->comment('支付金额');
            $table->string('pay_id')->default('')->comment('支付id');
            $table->string('pay_time')->default('')->comment('支付时间');
            $table->string('approve_time')->default('')->comment('商家确认时间');
            $table->string('confirm_time')->default('')->comment('用户核销使用时间');
            $table->string('finish_time')->default('')->comment('订单完成时间');
            $table->float('refund_amount')->comment('退款金额');
            $table->string('refund_id')->default('')->comment('微信退款id');
            $table->string('refund_type')->default('')->comment('退款方式');
            $table->string('refund_remarks')->default('')->comment('退款备注');
            $table->string('refund_time')->default('')->comment('退款时间');
            $table->string('remarks')->default('')->comment('订单备注');
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
        Schema::dropIfExists('set_meal_orders');
    }
}
