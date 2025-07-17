<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringShopDepositChangeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering_shop_deposit_change_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id')->comment('店铺id');
            $table->float('old_balance')->comment('变更前金额');
            $table->float('new_balance')->comment('变更后金额');
            $table->float('change_amount')->comment('变更金额');
            $table->integer('change_type')->comment('变更类型：1-商家充值，2-平台扣除');
            $table->string('reference_id')->default('')->comment('外部参考ID，如订单号');
            $table->string('remark')->default('')->comment('备注');
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
        Schema::dropIfExists('catering_shop_deposit_change_logs');
    }
}
