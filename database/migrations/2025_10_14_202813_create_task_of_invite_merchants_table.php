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
            $table->tinyInteger('status')->default(1)->comment('任务状态：1-进行中，2-已领取，3-已完成，4-已下架');

            $table->tinyInteger('merchant_type')->comment('商家类型：1-景点，2-酒店，3-餐饮，4-电商');
            $table->unsignedBigInteger('product_id')->nullable()->comment('产品id');
            $table->string('merchant_name', 200)->comment('商家名称');
            $table->string('tel', 50)->nullable()->comment('联系电话');

            $table->string('address', 255)->nullable()->comment('具体地址');
            $table->decimal('longitude', 9, 6)->nullable()->comment('经度');
            $table->decimal('latitude', 8, 6)->nullable()->comment('纬度');

            $table->unsignedDecimal('reward_total', 10, 2)->default(0)->comment('任务奖励总和');
            $table->json('reward_list')->comment('任务阶段奖励');

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
