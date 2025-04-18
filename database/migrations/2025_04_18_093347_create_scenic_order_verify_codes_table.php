<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScenicOrderVerifyCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scenic_order_verify_codes', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(0)->comment('核销状态：0-待核销，1-已核销, 2-已失效');
            $table->integer('order_id')->comment('订单id');
            $table->integer('scenic_id')->comment('景点id');
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
        Schema::dropIfExists('scenic_order_verify_codes');
    }
}
