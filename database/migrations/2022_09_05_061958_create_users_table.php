<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('avatar', 255)->comment('用户头像图片');
            $table->string('nickname', 50)->comment('用户昵称或网络名称');
            $table->string('mobile', 20)->unique()->comment('用户手机号码');
            $table->string('password', 255)->default('')->comment('登录密码');
            $table->string('openid', 64)->default('')->index()->comment('小程序openid');
            $table->tinyInteger('gender')->default(0)->comment('性别：0-未知，1-男，2-女');
            $table->string('bg', 255)->default('')->comment('背景图');
            $table->string('birthday')->nullable()->comment('生日');
            $table->string('constellation', 50)->default('')->comment('星座');
            $table->string('career', 50)->default('')->comment('职业');
            $table->text('signature')->nullable()->comment('签名');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE `users` comment '用户表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
