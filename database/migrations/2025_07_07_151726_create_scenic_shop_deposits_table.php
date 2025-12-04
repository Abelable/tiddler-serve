<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScenicShopDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scenic_shop_deposits', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->default(1)->comment('账户状态：1-正常，2-异常');
            $table->unsignedBigInteger('shop_id')->unique()->comment('店铺ID');
            $table->unsignedDecimal('balance', 10, 2)->default(0)->comment('保证金余额');
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
        Schema::dropIfExists('scenic_shop_deposits');
    }
}
