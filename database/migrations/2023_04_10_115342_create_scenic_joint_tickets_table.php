<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScenicJointTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scenic_joint_tickets', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户id');
            $table->integer('shop_id')->comment('店铺id');
            $table->string('scenic_ids')->comment('多个景点id');
            $table->integer('status')->default(0)->comment('申请状态：0-待审核，1-审核通过，2-审核未通过，3-下架');
            $table->string('failure_reason')->default('')->comment('审核失败原因');
            $table->string('image')->comment('列表图片');
            $table->longText('detail_image_list')->comment('详情图片列表');
            $table->string('name')->comment('商品名称');
            $table->float('price')->comment('商品价格');
            $table->float('market_price')->default(0)->comment('市场价格');
            $table->integer('stock')->comment('商品库存');
            $table->float('sales_commission_rate')->default(0.1)->comment('销售佣金比例');
            $table->float('promotion_commission_rate')->default(0.02)->comment('推广佣金比例');
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
        Schema::dropIfExists('scenic_joint_tickets');
    }
}
