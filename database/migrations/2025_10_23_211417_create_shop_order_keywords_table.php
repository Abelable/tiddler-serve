<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopOrderKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_order_keywords', function (Blueprint $table) {
            $table->id();
            $table->integer('product_type')->comment('产品类型');
            $table->integer('shop_id')->comment('店铺id');
            $table->string('content')->comment('搜索关键字内容');
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
        Schema::dropIfExists('shop_order_keywords');
    }
}
