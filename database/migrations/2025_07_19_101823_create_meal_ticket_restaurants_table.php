<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMealTicketRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meal_ticket_restaurants', function (Blueprint $table) {
            $table->id();
            $table->integer('meal_ticket_id')->comment('餐券id');
            $table->integer('restaurant_id')->comment('餐饮门店id');
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
        Schema::dropIfExists('meal_ticket_restaurants');
    }
}
