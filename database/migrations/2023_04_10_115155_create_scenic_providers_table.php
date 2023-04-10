<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScenicProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scenic_providers', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户id');
            $table->integer('status')->default(0)->comment('申请状态：0-待审核，1-审核通过（待支付），2-完成支付，3-审核失败');
            $table->string('failure_reason')->default('')->comment('审核失败原因');
            $table->string('company_name')->default('')->comment('企业名称');
            $table->string('region_desc')->comment('省市区描述');
            $table->string('region_code_list')->comment('省市区编码');
            $table->string('address_detail')->comment('地址详情');
            $table->string('business_license_photo')->default('')->comment('营业执照照片');
            $table->string('name')->comment('联系人姓名');
            $table->string('mobile')->comment('手机号');
            $table->string('email')->comment('邮箱');
            $table->string('id_card_number')->comment('身份证号');
            $table->string('id_card_front_photo')->comment('身份证正面照片');
            $table->string('id_card_back_photo')->comment('身份证反面照片');
            $table->string('hold_id_card_photo')->comment('手持身份证照片');
            $table->string('bank_card_owner_name')->comment('持卡人姓名');
            $table->string('bank_card_number')->comment('银行卡号');
            $table->string('bank_name')->comment('开户银行及支行名称');
            $table->string('shop_name')->comment('店铺名称');
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
        Schema::dropIfExists('scenic_providers');
    }
}
