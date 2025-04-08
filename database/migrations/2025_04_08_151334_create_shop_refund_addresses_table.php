<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopRefundAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_refund_addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id')->default(0)->comment('用户id');
            $table->string('consignee_name')->comment('收货人姓名');
            $table->string('mobile')->comment('手机号');
            $table->string('address_detail')->comment('收获地址');
            $table->string('supplement')->default('')->comment('补充说明');
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
        Schema::dropIfExists('shop_refund_addresses');
    }
}
