<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringProviderOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering_provider_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户id');
            $table->integer('provider_id')->comment('服务商id');
            $table->string('order_sn')->comment('订单编号');
            $table->integer('status')->default(0)->comment('订单状态：0-待支付，1-支付成功');
            $table->string('payment_amount')->comment('支付金额');
            $table->integer('pay_id')->default(0)->comment('支付id');
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
        Schema::dropIfExists('catering_provider_orders');
    }
}
