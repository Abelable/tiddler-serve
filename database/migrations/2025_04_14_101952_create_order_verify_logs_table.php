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
            $table->unsignedBigInteger('code_id')->index()->comment('核销码ID');
            $table->unsignedBigInteger('shop_id')->index()->comment('核销店铺ID');
            $table->unsignedBigInteger('verifier_id')->index()->comment('核销人员ID');
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
