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
            Schema::create('shop_incomes', function (Blueprint $table) {
                $table->id();

                // 收益状态
                $table->tinyInteger('status')->default(0)
                    ->comment('收益状态：0-订单待支付，1-待结算, 2-可提现，3-提现中，4-已结算');

                $table->unsignedBigInteger('withdrawal_id')->nullable()->index()->comment('提现记录id');

                // 店铺 & 订单关联
                $table->unsignedBigInteger('shop_id')->index()->comment('店铺id');
                $table->unsignedBigInteger('order_id')->index()->comment('订单id');
                $table->string('order_sn', 64)->index()->comment('订单编号');
                $table->unsignedBigInteger('goods_id')->index()->comment('商品id');

                // 金额字段（全部 unsignedDecimal）
                $table->unsignedDecimal('total_price', 10, 2)->default(0)->comment('总价');
                $table->tinyInteger('refund_status')->default(0)->comment('商品7天无理由：0-不支持，1-支持');
                $table->unsignedBigInteger('coupon_id')->default(0)->comment('优惠券id');
                $table->unsignedBigInteger('coupon_shop_id')->default(0)->comment('优惠券店铺id');
                $table->unsignedDecimal('coupon_denomination', 10, 2)->default(0)->comment('优惠券抵扣金额');
                $table->unsignedDecimal('freight_price', 10, 2)->default(0)->comment('运费');
                $table->unsignedDecimal('sales_amount', 10, 2)->default(0)->comment('销售额');
                $table->unsignedDecimal('income_base', 10, 2)->default(0)->comment('收入计算基数');
                $table->decimal('sales_commission_rate', 5, 2)->default(0)->comment('销售佣金比例');
                $table->unsignedDecimal('income_amount', 10, 2)->default(0)->comment('收入金额');

                $table->timestamps();
                $table->softDeletes();

                // 索引优化
                $table->index(['shop_id', 'status']);
            });

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
