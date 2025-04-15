<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_goods', function (Blueprint $table) {
            $table->id();
            $table->integer('scene')->default(1)->comment('场景值：1-添加购物车，2-直接购买');
            $table->integer('status')->default(1)
                ->comment('购物车商品状态：1-正常状态，2-所选规格库存为0、所选规格已不存在，3-商品库存为0、商品已下架、商品已删除');
            $table->string('status_desc')->default('')->comment('购物车商品状态描述');
            $table->integer('user_id')->comment('用户id');
            $table->integer('goods_id')->comment('商品id');
            $table->integer('shop_id')->comment('商品所属店铺id');
            $table->integer('shop_category_id')->comment('商品店铺分类id');
            $table->integer('freight_template_id')->comment('运费模板id');
            $table->integer('is_gift')->comment('是否为礼包商品：0-否，1-是');
            $table->integer('effective_duration')->default(0)->comment('有效时长（月）');
            $table->integer('refund_status')->comment('是否支持7天无理由：0-不支持，1-支持');
            $table->integer('delivery_mode')->default(1)->comment('提货方式：1-快递，2-自提，3-快递/自提');
            $table->string('cover')->comment('商品图片');
            $table->string('name')->comment('商品名称');
            $table->string('selected_sku_name')->default('')->comment('选中的规格名称');
            $table->integer('selected_sku_index')->default(-1)->comment('选中的规格索引');
            $table->float('price')->comment('商品价格');
            $table->float('market_price')->default(0)->comment('市场价格');
            $table->float('sales_commission_rate')->comment('销售佣金比例%');
            $table->float('promotion_commission_rate')->comment('推广佣金比例%');
            $table->float('promotion_commission_upper_limit')->comment('推广佣金上限');
            $table->float('superior_promotion_commission_rate')->comment('上级推广佣金比例%');
            $table->float('superior_promotion_commission_upper_limit')->comment('上级推广佣金上限');
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
        Schema::dropIfExists('cart_goods');
    }
}
