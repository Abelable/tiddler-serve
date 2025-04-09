<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopDepositPaymentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_deposit_payment_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(0)->comment('状态：0-待支付，1-支付成功');
            $table->integer('user_id')->comment('用户id');
            $table->integer('merchant_id')->comment('商家id');
            $table->integer('shop_id')->comment('店铺id');
            $table->float('payment_amount')->comment('支付金额');
            $table->integer('pay_id')->default(0)->comment('微信支付id');
            $table->string('pay_time')->default('')->comment('支付时间');
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
        Schema::dropIfExists('shop_deposit_payment_logs');
    }
}
