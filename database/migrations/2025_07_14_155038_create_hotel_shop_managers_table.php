<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelShopManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_shop_managers', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id')->comment('店铺id');
            $table->integer('user_id')->comment('用户id');
            $table->string('avatar')->default('')->comment('用户头像');
            $table->string('nickname')->default('')->comment('用户昵称');
            $table->integer('role_id')->comment('管理员角色id：1-超级管理员，2-运营，3-核销员');
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
        Schema::dropIfExists('hotel_shop_managers');
    }
}
