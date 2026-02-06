<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWxTransferLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wx_transfer_logs', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->default(0)->comment('奖品状态：0-已创建，待用户确认，1-转账成功, 2-转账失败');
            $table->string('fail_reason', 255)->nullable()->comment('失败原因');

            $table->unsignedBigInteger('user_id')->index()->comment('用户id');
            $table->string('openid', 64)->index()->comment('用户openid');

            $table->string('out_bill_no', 64)->unique()->comment('商户转账单号');
            $table->string('transfer_bill_no', 64)->nullable()->index()->comment('微信转账单号');

            $table->string('transfer_scene_id', 32)->comment('转账场景ID');
            $table->unsignedDecimal('transfer_amount', 10, 2)->comment('转账金额');

            $table->string('transfer_title', 255)->nullable()->comment('转账标题');
            $table->string('transfer_content', 255)->nullable()->comment('转账内容');

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
        Schema::dropIfExists('wx_transfer_logs');
    }
}
