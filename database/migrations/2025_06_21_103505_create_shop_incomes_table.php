<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopIncomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_incomes', function (Blueprint $table) {
            $table->id();
            $table->integer('withdrawal_id')->default(0)->comment('提现记录id');
            $table->integer('status')->default(0)->comment('收益状态：0-订单待支付，1-待结算, 2-可提现，3-提现中，4-已结算');
            $table->integer('user_id')->comment('用户id');
            $table->integer('order_id')->comment('订单id');
            $table->integer('goods_id')->comment('商品id');
            $table->integer('refund_status')->default(0)->comment('是否支持7天无理由：0-不支持，1-支持');
            $table->float('income_base')->comment('收入基数');
            $table->float('sales_commission_rate')->comment('销售佣金比例');
            $table->float('income_amount')->comment('收入金额');
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
        Schema::dropIfExists('shop_incomes');
    }
}
