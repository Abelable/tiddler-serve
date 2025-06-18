<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_deposits', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(1)->comment('账户状态：1-正常，2-异常');
            $table->integer('shop_id')->comment('店铺id');
            $table->float('balance')->default(0)->comment('保证金余额');
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
        Schema::dropIfExists('shop_deposits');
    }
}
