<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderVerifyCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_verify_codes', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->default(0)->comment('核销状态：0-待核销，1-已核销, 2-已失效');
            $table->unsignedBigInteger('order_id')->index()->comment('订单ID');
            $table->string('code', 32)->unique()->comment('核销码');
            $table->dateTime('expiration_time')->nullable()->comment('核销码失效时间');
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
        Schema::dropIfExists('order_verify_codes');
    }
}
