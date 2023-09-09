<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProviderRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provider_restaurants', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户id');
            $table->integer('provider_id')->comment('服务商id');
            $table->integer('restaurant_id')->comment('餐馆id');
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
        Schema::dropIfExists('provider_restaurants');
    }
}
