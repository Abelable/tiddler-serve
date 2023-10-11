<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderMealTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_meal_tickets', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->comment('订单id');
            $table->string('restaurant_name')->comment('门店名称');
            $table->integer('ticket_id')->comment('代金券id');
            $table->float('ticket_price')->comment('代金券售价');
            $table->float('ticket_original_price')->comment('代金券抵扣价格');
            $table->integer('number')->comment('代金券数量');
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
        Schema::dropIfExists('order_meal_tickets');
    }
}
