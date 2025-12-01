<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsCategoryShopCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_category_shop_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('goods_category_id');
            $table->unsignedBigInteger('shop_category_id');

            $table->foreign('goods_category_id')->references('id')->on('goods_categories')->cascadeOnDelete();
            $table->foreign('shop_category_id')->references('id')->on('shop_categories')->cascadeOnDelete();

            $table->unique(['goods_category_id', 'shop_category_id'], 'goods_category_shop_categories_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_category_shop_categories');
    }
}
