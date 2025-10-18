<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(1)
                ->comment('任务状态：1-进行中，2-已完成，待领取奖励，3-领取审核中，4-已领取，5-领取失败，6-已取消');
            $table->integer('step')->default(0)->comment('任务进度');
            $table->integer('user_id')->comment('用户id');
            $table->integer('task_id')->comment('任务id');
            $table->float('task_reward')->comment('任务奖励');
            $table->integer('product_type')->comment('产品类型：1-景点，2-酒店，3-餐饮，4-电商');
            $table->integer('product_id')->default(0)->comment('产品id');
            $table->integer('merchant_id')->default(0)->comment('商家id');
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
        Schema::dropIfExists('user_tasks');
    }
}
