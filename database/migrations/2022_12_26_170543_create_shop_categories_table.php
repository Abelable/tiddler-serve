<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('店铺分类名称');
            $table->integer('deposit')->comment('店铺保证金');
            $table->string('adapted_merchant_types')->comment('适配的商家类型：1-个人，2-企业');
            $table->integer('sort')->default(1)->comment('排序');
            $table->integer('visible')->default(1)->comment('状态: 0-隐藏,1-显示');
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
        Schema::dropIfExists('shop_categories');
    }
}
