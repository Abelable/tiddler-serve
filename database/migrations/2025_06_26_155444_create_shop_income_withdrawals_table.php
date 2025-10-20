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
            $table->integer('status')->default(0)->comment('状态：0-待审核；1-提现成功; 2-提现失败;');
            $table->string('failure_reason')->default('')->comment('提现失败原因');
            $table->integer('user_id')->comment('用户id');
            $table->integer('merchant_type')->comment('商家类型：1-景点，2-酒店，3-餐饮，4-电商');
            $table->integer('shop_type')->default(1)->comment('店铺类型：1-企业，2-个人');
            $table->integer('shop_id')->comment('店铺id');
            $table->float('withdraw_amount')->comment('提现金额');
            $table->float('tax_fee')->default(0)->comment('税费');
            $table->float('handling_fee')->comment('手续费');
            $table->float('actual_amount')->comment('实际到账金额');
            $table->integer('path')->comment('提现方式：1-微信；2-银行卡；3-余额');
            $table->string('remark')->default('')->comment('备注');
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
