<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScenicMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scenic_merchants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->comment('用户ID');
            $table->tinyInteger('status')->default(0)
                ->comment('申请状态：0-待审核，1-审核通过（待支付保证金），2-已支付保证金，3-审核失败');
            $table->string('failure_reason', 255)->default('')
                ->comment('审核失败原因');

            // 公司信息
            $table->string('company_name', 255)->comment('公司名称');
            $table->string('business_license_photo', 255)->comment('营业执照照片');

            // 地址信息
            $table->string('region_desc', 255)->comment('省市区描述');
            $table->json('region_code_list')->comment('省市区编码列表');
            $table->string('address_detail', 255)->comment('地址详情');

            // 联系人信息
            $table->string('name', 50)->comment('联系人姓名');
            $table->string('mobile', 20)->index()->comment('手机号');
            $table->string('email', 100)->default('')->comment('邮箱');

            // 身份证信息
            $table->string('id_card_number', 32)->comment('身份证号');
            $table->string('id_card_front_photo', 255)->comment('身份证正面');
            $table->string('id_card_back_photo', 255)->comment('身份证反面');
            $table->string('hold_id_card_photo', 255)->comment('手持身份证');

            // 银行卡信息
            $table->string('bank_card_owner_name', 50)->comment('持卡人姓名');
            $table->string('bank_card_number', 64)->comment('银行卡号');
            $table->string('bank_name', 255)->comment('开户银行及支行名称');

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
        Schema::dropIfExists('scenic_merchants');
    }
}
