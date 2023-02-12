<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->integer('scene')->default(1)->comment('场景值：1-添加购物车，2-直接购买');
            $table->integer('status')->default(1)
                ->comment('购物车商品状态：1-正常状态，2-所选规格库存为0、所选规格已不存在，3-商品库存为0、商品已下架、商品已删除');
            $table->string('status_desc')->default('')->comment('购物车商品状态描述');
            $table->integer('user_id')->comment('用户id');
            $table->integer('shop_id')->comment('商品所属店铺id');
            $table->integer('goods_id')->comment('商品id');
            $table->integer('goods_category_id')->comment('商品分类id');
            $table->string('goods_image')->comment('商品图片');
            $table->string('goods_name')->comment('商品名称');
            $table->string('selected_sku_name')->default('')->comment('选中的规格名称');
            $table->integer('selected_sku_index')->default(-1)->comment('选中的规格索引');
            $table->float('price')->comment('商品价格');
            $table->float('market_price')->default(0)->comment('市场价格');
            $table->integer('number')->comment('商品数量');
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
        Schema::dropIfExists('carts');
    }
}
