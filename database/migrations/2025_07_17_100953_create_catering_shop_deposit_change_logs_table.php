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
            $table->unsignedBigInteger('shop_id')->index()->comment('店铺ID');
            $table->tinyInteger('change_type')->comment('变更类型：1-商家充值，2-平台扣除');

            $table->unsignedDecimal('old_balance', 10, 2)->default(0)
                ->comment('变更前金额');
            $table->unsignedDecimal('new_balance', 10, 2)->default(0)
                ->comment('变更后金额');
            $table->unsignedDecimal('change_amount', 10, 2)->default(0)
                ->comment('变更金额');

            $table->string('reference_id', 64)->default('')
                ->comment('外部参考ID，例如微信支付单号、订单号');
            $table->string('remark', 255)->default('')->comment('备注');

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
