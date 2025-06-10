<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopPickupAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_pickup_addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id')->default(0)->comment('店铺id');
            $table->string('name')->default('')->comment('提货门店名称');
            $table->string('address_detail')->comment('提货门店地址详情');
            $table->decimal('longitude', 9, 6)->comment('提货点经度');
            $table->decimal('latitude', 8, 6)->comment('提货点纬度');
            $table->longText('open_time_list')->comment('提货门店营业时间');
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
        Schema::dropIfExists('shop_pickup_addresses');
    }
}
