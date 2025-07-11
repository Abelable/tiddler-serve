<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScenicShopIncomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scenic_shop_incomes', function (Blueprint $table) {
            $table->id();
            $table->integer('withdrawal_id')->default(0)->comment('提现记录id');
            $table->integer('status')->default(0)->comment('收益状态：0-订单待支付，1-待结算, 2-可提现，3-提现中，4-已结算');
            $table->integer('shop_id')->comment('店铺id');
            $table->integer('order_id')->comment('订单id');
            $table->string('order_sn')->comment('订单编号');
            $table->integer('ticket_id')->comment('门票id');
            $table->integer('refund_status')->default(0)->comment('退票状态：1-随时可退，2-有条件退，3-不可退');
            $table->float('payment_amount')->comment('支付金额');
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
        Schema::dropIfExists('scenic_shop_incomes');
    }
}
