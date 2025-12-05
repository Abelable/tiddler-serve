<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->default(0)->comment('佣金状态：0-订单待支付，1-待结算, 2-可提现，3-提现中，4-已结算');
            $table->integer('withdrawal_id')->nullable()->index()->comment('提现记录id');
            $table->tinyInteger('scene')->comment('场景：1-自购 2-直推分享 3-间推分享 4-直推团队 5-间推团队');
            $table->integer('promoter_id')->index()->comment('代言人id');
            $table->tinyInteger('promoter_level')->comment('代言人等级');
            $table->integer('user_id')->index()->comment('用户id');
            $table->integer('order_id')->index()->comment('订单id');
            $table->string('order_sn')->index()->comment('订单编号');
            $table->tinyInteger('product_type')->comment('产品类型：1-景点，2-酒店，4-商品，5-套餐，6-餐券');
            $table->integer('product_id')->index()->comment('产品id');

            $table->unsignedDecimal('achievement', 10, 2)->default(0)->comment('业绩：平台活动-订单商品总价，非平台活动-订单支付金额');
            $table->unsignedDecimal('commission_base', 10, 2)->default(0)->comment('佣金基数');
            $table->decimal('commission_rate', 5, 2)->default(0)->comment('佣金比例');
            $table->unsignedDecimal('commission_limit', 10, 2)->default(0)->comment('佣金上限');
            $table->unsignedDecimal('commission_amount', 10, 2)->default(0)->comment('佣金金额');

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
        Schema::dropIfExists('commissions');
    }
}
