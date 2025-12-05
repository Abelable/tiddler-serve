<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSetMealVerifyLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('set_meal_verify_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('code_id')->index()->comment('核销码ID');
            $table->unsignedBigInteger('restaurant_id')->index()->comment('核销餐馆id');
            $table->unsignedBigInteger('verifier_id')->index()->comment('核销人员id');
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
        Schema::dropIfExists('set_meal_verify_logs');
    }
}
