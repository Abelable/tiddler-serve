<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskOfInviteMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_of_invite_merchants', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(1)
                ->comment('任务状态：1-进行中，2-已领取，3-已完成，4-已下架');
            $table->integer('product_type')->comment('产品类型：1-景点，2-酒店，3-餐饮，4-电商');
            $table->integer('product_id')->default(0)->comment('产品id');
            $table->string('product_name')->comment('产品名称');
            $table->string('tel')->default('')->comment('联系电话');
            $table->string('address')->default('')->comment('具体地址');
            $table->decimal('longitude', 9, 6)->default(0)->comment('经度');
            $table->decimal('latitude', 8, 6)->default(0)->comment('纬度');
            $table->float('reward_total')->comment('任务奖励总和');
            $table->string('reward_list')->comment('任务阶段奖励');
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
        Schema::dropIfExists('task_of_invite_merchants');
    }
}
