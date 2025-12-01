<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->default(0)->comment('申请状态：0-待审核，1-审核通过，2-审核未通过，3-下架');
            $table->string('failure_reason')->nullable()->comment('审核失败原因');
            $table->integer('shop_id')->default(0)->index()->comment('店铺id');
            $table->string('cover')->comment('列表图片');
            $table->string('video')->nullable()->comment('主图视频');
            $table->longText('image_list')->comment('主图图片列表');
            $table->longText('detail_image_list')->comment('详情图片列表');
            $table->string('default_spec_image')->comment('默认规格图片');
            $table->string('name')->comment('商品名称');
            $table->string('introduction')->nullable()->comment('商品介绍');
            $table->integer('freight_template_id')->default(0)->comment('运费模板id：0-包邮');

            // 金额字段
            $table->unsignedDecimal('price', 10, 2)->comment('商品价格');
            $table->unsignedDecimal('market_price', 10, 2)->default(0)->comment('市场价格');

            // 佣金字段
            $table->decimal('sales_commission_rate', 5, 2)->default(0)->comment('销售佣金比例%（如15.5表示15.5%）');
            $table->decimal('promotion_commission_rate', 5, 2)->default(0)->comment('推广佣金比例%（如10.5表示10.5%）');
            $table->unsignedDecimal('promotion_commission_upper_limit', 10, 2)->default(0)->comment('推广佣金上限');
            $table->decimal('superior_promotion_commission_rate', 5, 2)->default(0)->comment('上级推广佣金比例%');
            $table->unsignedDecimal('superior_promotion_commission_upper_limit', 10, 2)->default(0)->comment('上级推广佣金上限');

            // 库存和限购
            $table->integer('stock')->default(0)->comment('商品库存');
            $table->integer('number_limit')->default(0)->comment('限购数量');

            // 规格和 SKU
            $table->longText('spec_list')->comment('商品规格列表');
            $table->longText('sku_list')->comment('商品sku');

            // 配送和退款
            $table->tinyInteger('delivery_mode')->default(1)->comment('提货方式：1-快递，2-自提，3-快递/自提');
            $table->tinyInteger('refund_status')->default(0)->comment('是否支持7天无理由：0-不支持，1-支持');
            $table->integer('refund_address_id')->default(0)->comment('退货地址id');

            // 销售与数据统计
            $table->integer('sales_volume')->default(0)->comment('销量');
            $table->decimal('score', 3, 2)->default(0)->comment('评分');
            $table->integer('views')->default(0)->comment('点击率');

            // 时间
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
        Schema::dropIfExists('goods');
    }
}
