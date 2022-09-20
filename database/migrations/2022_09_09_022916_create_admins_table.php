<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('avatar')->default('')->comment('管理员头像');
            $table->string('nickname')->default('')->comment('管理员昵称');
            $table->string('account')->unique()->comment('管理员账号');
            $table->string('password')->comment('管理员账号密码');
            $table->integer('role_id')->comment('管理员角色id');
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
        Schema::dropIfExists('admins');
    }
}
