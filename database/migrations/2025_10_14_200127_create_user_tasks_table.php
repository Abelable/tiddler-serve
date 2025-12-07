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
            $table->tinyInteger('status')->default(1)
                ->comment('任务状态：1-进行中，2-已完成，待领取奖励，3-领取审核中，4-已领取，5-领取失败，6-已取消');
            $table->tinyInteger('step')->default(0)->comment('任务进度');

            $table->unsignedBigInteger('user_id')->index()->comment('用户id');
            $table->unsignedBigInteger('task_id')->index()->comment('任务id');

            $table->unsignedDecimal('task_reward', 10, 2)->default(0)->comment('任务奖励');

            $table->tinyInteger('merchant_type')->comment('商家类型：1-景点，2-酒店，3-餐饮，4-电商');
            $table->unsignedBigInteger('merchant_id')->nullable()->comment('商家id');
            $table->unsignedBigInteger('order_id')->nullable()->comment('订单id');
            $table->unsignedBigInteger('product_id')->nullable()->comment('产品id');
            $table->tinyInteger('product_type')->nullable()->comment('产品类型：5-餐券，6-套餐');

            $table->dateTime('pick_time')->nullable()->comment('领取时间');
            $table->dateTime('finish_time')->nullable()->comment('完成时间');

            $table->unsignedBigInteger('withdrawal_id')->nullable()->comment('提现记录id');

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
