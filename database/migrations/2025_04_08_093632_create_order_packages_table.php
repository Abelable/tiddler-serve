<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_packages', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->default(0)->comment('包裹状态：0-待发货，1-已发货，2-运输中，3-已签收');
            $table->unsignedBigInteger('order_id')->index()->comment('订单id');
            $table->string('ship_channel', 50)->comment('快递公司名称');
            $table->string('ship_code', 20)->comment('快递公司编号');
            $table->string('ship_sn', 50)->index()->comment('快递单号');
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
        Schema::dropIfExists('order_packages');
    }
}
