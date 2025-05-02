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
            $table->integer('withdrawal_id')->default(0)->comment('提现记录id');
            $table->integer('status')->default(0)->comment('佣金状态：0-订单待支付，1-待结算, 2-可提现，3-提现中，4-已结算');
            $table->integer('scene')->comment('场景：1-自购 2-直推分享 3-间推分享 4-直推团队 5-间推团队');
            $table->integer('promoter_id')->comment('推广员id');
            $table->integer('promoter_level')->comment('推广员等级');
            $table->integer('user_id')->comment('用户id');
            $table->integer('order_id')->comment('订单id');
            $table->integer('product_type')->comment('产品类型：1-景点，2-酒店，3-餐馆，4-商品');
            $table->integer('product_id')->comment('产品id');
            $table->integer('refund_status')->default(0)->comment('是否支持7天无理由：0-不支持，1-支持');
            $table->float('commission_base')->comment('佣金基数');
            $table->float('commission_rate')->comment('佣金系数');
            $table->float('commission_limit')->default(0)->comment('佣金上限');
            $table->float('commission_amount')->comment('佣金金额');
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
