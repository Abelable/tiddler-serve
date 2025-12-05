<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScenicOrderVerifyLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scenic_order_verify_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('code_id')->index()->comment('核销码ID');
            $table->unsignedBigInteger('scenic_id')->index()->comment('核销景点id');
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
        Schema::dropIfExists('scenic_order_verify_logs');
    }
}
