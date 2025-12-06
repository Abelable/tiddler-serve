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
            $table->tinyInteger('status')->default(0)->comment('核销状态：0-待核销，1-已核销, 2-已失效');

            $table->unsignedBigInteger('shop_id')->index()->comment('餐饮门店id');
            $table->unsignedBigInteger('order_id')->index()->comment('订单id');

            $table->string('code', 32)->unique()->comment('核销码');
            $table->dateTime('expiration_time')->nullable()->comment('核销码失效时间');

            $table->unsignedBigInteger('verifier_id')->nullable()->index()->comment('核销人员ID');
            $table->dateTime('verify_time')->nullable()->comment('核销时间');

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
