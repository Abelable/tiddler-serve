<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsGoodsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_goods_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('goods_id');
            $table->unsignedBigInteger('goods_category_id');

            $table->foreign('goods_id')->references('id')->on('goods')->cascadeOnDelete();
            $table->foreign('goods_category_id')->references('id')->on('goods_categories')->cascadeOnDelete();

            $table->unique(['goods_id', 'goods_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods_goods_categories');
    }
}
