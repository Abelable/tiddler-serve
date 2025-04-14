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
            $table->integer('verify_code_id')->comment('核销码id');
            $table->string('verify_time')->comment('核销时间');
            $table->integer('shop_id')->comment('核销店铺id');
            $table->integer('verifier_id')->comment('核销人员id');
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
        Schema::dropIfExists('order_verify_logs');
    }
}
