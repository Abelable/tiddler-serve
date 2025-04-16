<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_goods', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(0)->comment('状态：0-待付款，1-已付款，2-已退款');
            $table->integer('user_id')->comment('用户id');
            $table->integer('user_level')->comment('用户等级');
            $table->integer('order_id')->comment('订单id');
            $table->integer('shop_id')->default(0)->comment('店铺id');
            $table->integer('goods_id')->comment('商品id');
            $table->integer('is_gift')->default(0)->comment('是否为礼包商品：0-否，1-是');
            $table->integer('effective_duration')->default(0)->comment('有效时长（月）');
            $table->integer('refund_status')->comment('是否支持7天无理由：0-不支持，1-支持');
            $table->string('cover')->comment('列表图片');
            $table->string('name')->comment('商品名称');
            $table->float('price')->comment('商品价格');
            $table->float('sales_commission_rate')->comment('销售佣金比例%');
            $table->float('promotion_commission_rate')->comment('推广佣金比例%');
            $table->float('promotion_commission_upper_limit')->comment('推广佣金上限');
            $table->float('superior_promotion_commission_rate')->comment('上级推广佣金比例%');
            $table->float('superior_promotion_commission_upper_limit')->comment('上级推广佣金上限');
            $table->string('selected_sku_name')->comment('选中的规格名称');
            $table->integer('selected_sku_index')->comment('选中的规格索引');
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
        Schema::dropIfExists('order_goods');
    }
}
