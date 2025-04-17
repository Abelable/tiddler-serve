<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScenicOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scenic_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_sn')->comment('订单编号');
            $table->integer('status')->comment('订单状态');
            $table->integer('user_id')->comment('用户id');
            $table->string('consignee')->comment('出游人姓名');
            $table->string('mobile')->comment('出游人手机号');
            $table->string('id_card_number')->comment('出游人身份证号');
            $table->integer('shop_id')->default(0)->comment('店铺id');
            $table->string('shop_logo')->default('')->comment('店铺logo');
            $table->string('shop_name')->default('')->comment('店铺名称');
            $table->float('payment_amount')->comment('支付金额');
            $table->integer('pay_id')->default(0)->comment('支付id');
            $table->string('pay_time')->default('')->comment('支付时间');
            $table->string('confirm_time')->default('')->comment('用户确认收货时间');
            $table->string('finish_time')->default('')->comment('订单关闭时间');
            $table->float('refund_amount')->comment('退款金额');
            $table->string('refund_type')->default('')->comment('退款方式');
            $table->string('refund_remarks')->default('')->comment('退款备注');
            $table->string('refund_time')->default('')->comment('退款时间');
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
        Schema::dropIfExists('scenic_orders');
    }
}
