<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->comment('用户ID');
            $table->tinyInteger('status')->default(0)
                ->comment('申请状态：0-待审核，1-审核通过（待支付保证金），2-已支付保证金，3-审核失败');
            $table->string('failure_reason', 255)->default('')
                ->comment('审核失败原因');

            $table->tinyInteger('type')->default(1)->comment('商家类型：1-企业，2-个人');
            $table->string('company_name', 255)->default('')->comment('企业名称');

            // 地址信息（使用 JSON 更规范）
            $table->string('region_desc', 255)->nullable()->comment('省市区描述');
            $table->json('region_code_list')->nullable()->comment('省市区编码列表');
            $table->string('address_detail', 255)->comment('地址详情');

            // 营业执照
            $table->string('business_license_photo', 255)->default('')->comment('营业执照照片');

            // 联系人信息
            $table->string('name', 50)->comment('联系人姓名');
            $table->string('mobile', 20)->index()->comment('手机号');
            $table->string('email', 100)->default('')->comment('邮箱');

            // 身份证信息
            $table->string('id_card_number', 32)->default('')->comment('身份证号');
            $table->string('id_card_front_photo', 255)->default('')->comment('身份证正面');
            $table->string('id_card_back_photo', 255)->default('')->comment('身份证反面');
            $table->string('hold_id_card_photo', 255)->default('')->comment('手持身份证');

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
        Schema::dropIfExists('merchants');
    }
}
