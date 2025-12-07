<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopIncomeWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_income_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->default(0)->comment('状态：0-待审核；1-提现成功; 2-提现失败;');
            $table->string('failure_reason', 255)->nullable()->comment('提现失败原因');

            $table->unsignedBigInteger('user_id')->index()->comment('用户id');
            $table->tinyInteger('merchant_type')->comment('商家类型：1-景点，2-酒店，3-餐饮，4-电商');
            $table->tinyInteger('shop_type')->default(1)->comment('店铺类型：1-企业，2-个人');
            $table->unsignedBigInteger('shop_id')->index()->comment('店铺id');

            $table->unsignedDecimal('withdraw_amount', 10, 2)->default(0)->comment('提现金额');
            $table->unsignedDecimal('tax_fee', 10, 2)->default(0)->comment('税费');
            $table->unsignedDecimal('handling_fee', 10, 2)->default(0)->comment('手续费');
            $table->unsignedDecimal('actual_amount', 10, 2)->default(0)->comment('实际到账金额');

            $table->tinyInteger('path')->default(2)->comment('提现方式：1-微信；2-银行卡；3-余额');
            $table->string('remark', 500)->nullable()->comment('备注');

            $table->unsignedBigInteger('reviewer_id')->nullable()->comment('审核管理员ID');
            $table->dateTime('reviewed_at')->nullable()->comment('审核时间');

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
        Schema::dropIfExists('shop_income_withdrawals');
    }
}
