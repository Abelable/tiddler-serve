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
            $table->integer('is_default')->default(0)->comment('是否为默认地址');
            $table->string('name')->comment('联系人姓名');
            $table->string('mobile')->comment('手机号');
            $table->string('region_desc')->comment('省市区描述');
            $table->string('region_code_list')->comment('省市区编码');
            $table->string('address_detail')->comment('地址详情');
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
