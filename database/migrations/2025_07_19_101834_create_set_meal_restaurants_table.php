<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSetMealRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('set_meal_restaurants', function (Blueprint $table) {
            $table->id();
            $table->integer('set_meal_id')->comment('餐券id');
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
        Schema::dropIfExists('set_meal_restaurants');
    }
}
