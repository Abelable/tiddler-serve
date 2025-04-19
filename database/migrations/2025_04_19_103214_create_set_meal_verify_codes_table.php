<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSetMealVerifyCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('set_meal_verify_codes', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(0)->comment('核销状态：0-待核销，1-已核销, 2-已失效');
            $table->integer('order_id')->comment('订单id');
            $table->integer('restaurant_id')->comment('餐馆id');
            $table->string('code')->unique()->comment('核销码');
            $table->string('expiration_time')->default('')->comment('核销码失效时间');
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
        Schema::dropIfExists('set_meal_verify_codes');
    }
}
