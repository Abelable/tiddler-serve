<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_category_id')->default(0)->comment('店铺分类id');
            $table->string('name')->comment('商品分类名称');
            $table->integer('min_sales_commission_rate')->default(0)->comment('最小销售佣金比例');
            $table->integer('max_sales_commission_rate')->default(0)->comment('最大销售佣金比例');
            $table->integer('min_promotion_commission_rate')->default(0)->comment('最小推广佣金比例');
            $table->integer('max_promotion_commission_rate')->default(0)->comment('最大推广佣金比例');
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
        Schema::dropIfExists('goods_categories');
    }
}
