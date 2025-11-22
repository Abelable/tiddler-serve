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
            $table->integer('status')->default(0)->comment('申请状态：0-待审核，1-审核通过，2-审核未通过，3-下架');
            $table->string('failure_reason')->default('')->comment('审核失败原因');
            $table->integer('shop_id')->default(0)->comment('店铺id');
            $table->integer('shop_category_id')->comment('所属店铺分类id');
            $table->integer('category_id')->comment('商品分类id');
            $table->string('cover')->comment('列表图片');
            $table->string('video')->default('')->comment('主图视频');
            $table->longText('image_list')->comment('主图图片列表');
            $table->longText('detail_image_list')->comment('详情图片列表');
            $table->string('default_spec_image')->comment('默认规格图片');
            $table->string('name')->comment('商品名称');
            $table->string('introduction')->default('')->comment('商品介绍');
            $table->integer('freight_template_id')->default(0)->comment('运费模板id：0-包邮');
            $table->float('price')->comment('商品价格');
            $table->float('market_price')->default(0)->comment('市场价格');
            $table->float('sales_commission_rate')->default(0)->comment('销售佣金比例%');
            $table->float('promotion_commission_rate')->default(0)->comment('推广佣金比例%');
            $table->float('promotion_commission_upper_limit')->default(0)->comment('推广佣金上限');
            $table->float('superior_promotion_commission_rate')->default(0)->comment('上级推广佣金比例%');
            $table->float('superior_promotion_commission_upper_limit')->default(0)->comment('上级推广佣金上限');
            $table->integer('stock')->comment('商品库存');
            $table->integer('number_limit')->default(0)->comment('限购数量');
            $table->longText('spec_list')->comment('商品规格列表');
            $table->longText('sku_list')->comment('商品sku');
            $table->integer('delivery_mode')->default(1)->comment('提货方式：1-快递，2-自提，3-快递/自提');
            $table->integer('refund_status')->default(0)->comment('是否支持7天无理由：0-不支持，1-支持');
            $table->integer('refund_address_id')->default(0)->comment('退货地址id');
            $table->integer('sales_volume')->default(0)->comment('销量');
            $table->float('score')->default(0)->comment('评分');
            $table->integer('views')->default(0)->comment('点击率');
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
