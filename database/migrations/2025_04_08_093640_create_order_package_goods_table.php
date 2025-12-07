<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPackageGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_package_goods', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id')->index()->comment('订单ID');
            $table->unsignedBigInteger('package_id')->index()->comment('包裹ID');
            $table->unsignedBigInteger('goods_id')->index()->comment('商品ID');

            $table->string('cover', 500)->comment('商品图片');
            $table->string('name', 200)->comment('商品名称');
            $table->string('selected_sku_name', 200)->default('')->comment('规格名称');

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
        Schema::dropIfExists('order_package_goods');
    }
}
