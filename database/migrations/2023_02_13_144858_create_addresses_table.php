<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->comment('用户ID');
            $table->tinyInteger('is_default')
                ->default(0)
                ->index()
                ->comment('是否为默认地址：0-否 1-是');
            $table->string('name', 50)->comment('联系人姓名');
            $table->string('mobile', 20)->comment('手机号');
            $table->string('region_desc', 255)->comment('省市区描述');
            $table->json('region_code_list')->comment('省市区编码数组');
            $table->string('address_detail', 255)->comment('详细地址');
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
        Schema::dropIfExists('addresses');
    }
}
