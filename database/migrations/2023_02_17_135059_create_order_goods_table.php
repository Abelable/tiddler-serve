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
            $table->tinyInteger('status')->default(0)->comment('状态：0-待付款，1-已付款，2-已退款');

            $table->unsignedBigInteger('user_id')->index()->comment('用户id');
            $table->tinyInteger('user_level')->comment('用户等级');
            $table->tinyInteger('promoter_status')->comment('用户代言人身份状态');

            $table->unsignedBigInteger('order_id')->index()->comment('订单id');
            $table->unsignedBigInteger('shop_id')->default(0)->index()->comment('店铺id');
            $table->unsignedBigInteger('goods_id')->index()->comment('商品id');

            $table->tinyInteger('is_gift')->default(0)->comment('是否为礼包商品：0-否，1-是');
            $table->unsignedInteger('duration')->nullable()->comment('代言时长（天）');
            $table->tinyInteger('refund_status')->comment('是否支持7天无理由：0-不支持，1-支持');
            $table->unsignedBigInteger('refund_address_id')->nullable()->comment('退货地址');

            $table->string('cover', 500)->comment('商品图片');
            $table->string('name', 200)->comment('商品名称');

            $table->unsignedDecimal('price', 10, 2)->default(0)->comment('商品价格');
            $table->decimal('sales_commission_rate', 5, 2)->default(0)->comment('销售佣金比例%');
            $table->decimal('promotion_commission_rate', 5, 2)->default(0)->comment('推广佣金比例%');
            $table->unsignedDecimal('promotion_commission_upper_limit', 10, 2)->default(0)->comment('推广佣金上限');
            $table->decimal('superior_promotion_commission_rate', 5, 2)->default(0)->comment('上级推广佣金比例%');
            $table->unsignedDecimal('superior_promotion_commission_upper_limit', 10, 2)->default(0)->comment('上级推广佣金上限');

            $table->string('selected_sku_name', 200)->default('')->comment('选中的规格名称');
            $table->unsignedInteger('selected_sku_index')->default(0)->comment('选中的规格索引');
            $table->unsignedInteger('number')->default(1)->comment('商品数量');

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
