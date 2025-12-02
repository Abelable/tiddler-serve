<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderVerifyLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_verify_logs', function (Blueprint $table) {
            $table->id();

            // 核销相关
            $table->unsignedBigInteger('verify_code_id')->index()->comment('核销码ID');
            $table->dateTime('verify_time')->comment('核销时间');

            // 店铺与人员
            $table->unsignedBigInteger('shop_id')->index()->comment('核销店铺ID');
            $table->unsignedBigInteger('verifier_id')->index()->comment('核销人员ID');

            $table->timestamps();
            $table->softDeletes();

            // 索引优化
            $table->index(['verify_code_id', 'shop_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_verify_logs');
    }
}
