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

            // 场景与状态
            $table->tinyInteger('scene')->default(1)->comment('场景：1-添加购物车，2-直接购买');
            $table->tinyInteger('status')->default(1)->comment('购物车商品状态：1-正常，2-SKU失效，3-商品失效');
            $table->string('status_desc', 200)->default('')->comment('购物车商品状态描述');

            // 用户与商品关联
            $table->unsignedBigInteger('user_id')->index()->comment('用户id');
            $table->unsignedBigInteger('shop_id')->default(0)->index()->comment('店铺id');
            $table->unsignedBigInteger('goods_id')->index()->comment('商品id');

            // 商品属性
            $table->unsignedBigInteger('freight_template_id')->default(0)->comment('运费模板id');
            $table->boolean('is_gift')->default(false)->comment('是否为礼包商品');
            $table->unsignedInteger('duration')->default(0)->comment('代言时长（天）');
            $table->tinyInteger('refund_status')->default(1)->comment('是否支持7天无理由退货：0-不支持，1-支持');
            $table->unsignedBigInteger('refund_address_id')->default(0)->comment('退货地址id');
            $table->tinyInteger('delivery_mode')->default(1)->comment('提货方式：1-快递，2-自提，3-快递/自提');

            // 商品展示信息
            $table->string('cover', 500)->comment('商品图片');
            $table->string('name', 200)->comment('商品名称');
            $table->string('selected_sku_name', 200)->default('')->comment('选中的规格名称');
            $table->integer('selected_sku_index')->default(-1)->comment('选中的规格索引');

            // 金额字段
            $table->unsignedDecimal('price', 10, 2)->comment('商品价格');
            $table->unsignedDecimal('market_price', 10, 2)->default(0)->comment('市场价格');

            // 佣金字段
            $table->decimal('sales_commission_rate', 5, 2)->default(0)->comment('销售佣金比例%');
            $table->decimal('promotion_commission_rate', 5, 2)->default(0)->comment('推广佣金比例%');
            $table->unsignedDecimal('promotion_commission_upper_limit', 10, 2)->default(0)->comment('推广佣金上限');
            $table->decimal('superior_promotion_commission_rate', 5, 2)->default(0)->comment('上级推广佣金比例%');
            $table->unsignedDecimal('superior_promotion_commission_upper_limit', 10, 2)->default(0)->comment('上级推广佣金上限');

            // 数量字段
            $table->unsignedInteger('number')->default(1)->comment('商品数量');

            $table->timestamps();

            // 索引优化
            $table->index(['user_id', 'goods_id']);
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
