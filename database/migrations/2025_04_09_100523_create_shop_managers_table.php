<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_managers', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id')->comment('店铺id');
            $table->integer('role_id')->comment('管理员角色id');
            $table->integer('user_id')->comment('用户id');
            $table->string('avatar')->default('')->comment('用户头像');
            $table->string('nickname')->default('')->comment('用户昵称');
            $table->string('mobile')->default('')->comment('联系方式');
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
        Schema::dropIfExists('shop_managers');
    }
}
