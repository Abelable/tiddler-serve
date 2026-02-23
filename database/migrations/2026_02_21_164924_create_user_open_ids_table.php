<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserOpenIdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_open_ids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('关联用户ID');
            $table->string('openid', 64)->index()->comment('小程序openid');
            $table->string('app_id', 64)->comment('小程序AppID');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'app_id'], 'user_app_unique');

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
        Schema::dropIfExists('user_open_ids');
    }
}
