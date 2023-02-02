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
            $table->integer('user_id')->comment('用户id');
            $table->integer('shop_id')->comment('店铺id');
            $table->integer('status')->default(0)->comment('申请状态：0-待审核，1-审核通过，2-审核未通过，3-下架');
            $table->string('failure_reason')->default('')->comment('审核失败原因');
            $table->string('video')->default('')->comment('主图视频');
            $table->string('image_list')->comment('主图图片');
            $table->string('name')->comment('商品名称');
            $table->integer('freight_template_id')->default(0)->comment('运费模板id：0-包邮');
            $table->integer('category_id')->comment('商品分类id');
            $table->integer('return_address_id')->comment('退货地址id');
            $table->float('price')->comment('商品价格');
            $table->float('market_price')->default(0)->comment('市场价格');
            $table->integer('stock')->comment('商品库存');
            $table->float('commission_rate')->default(0)->comment('推广佣金比例');
            $table->string('detail_image_list')->comment('商品详情图片');
            $table->longText('spec_list')->comment('商品规格列表，使用场景：编辑商品信息');
            $table->longText('sku_list')->comment('商品sku，使用场景：购买商品');
            $table->integer('sales_volume')->default(0)->comment('商品销量');
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
