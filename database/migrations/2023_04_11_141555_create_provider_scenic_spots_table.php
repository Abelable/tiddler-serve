<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProviderScenicSpotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provider_scenic_spots', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('供应商id');
            $table->integer('provider_id')->comment('供应商id');
            $table->integer('scenic_id')->comment('景点id');
            $table->integer('status')->default(0)->comment('申请状态：0-待审核，1-审核通过，2-审核失败');
            $table->string('failure_reason')->default('')->comment('审核失败原因');
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
        Schema::dropIfExists('provider_scenic_spots');
    }
}
