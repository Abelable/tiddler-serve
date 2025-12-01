<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_categories', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->default('')->comment('商品分类图标');
            $table->string('name')->comment('商品分类名称');
            $table->string('description')->default('')->comment('商品分类描述');
            $table->decimal('min_sales_commission_rate', 5, 2)->default(0)->comment('最小销售佣金比例');
            $table->decimal('max_sales_commission_rate', 5, 2)->default(0)->comment('最大销售佣金比例');
            $table->decimal('min_promotion_commission_rate', 5, 2)->default(0)->comment('最小推广佣金比例');
            $table->decimal('max_promotion_commission_rate', 5, 2)->default(0)->comment('最大推广佣金比例');
            $table->unsignedDecimal('promotion_commission_upper_limit', 10, 2)->default(0)->comment('推广佣金上限（元）');
            $table->decimal('min_superior_promotion_commission_rate', 5, 2)->default(0)->comment('最小上级推广佣金比例');
            $table->decimal('max_superior_promotion_commission_rate', 5, 2)->default(0)->comment('最大上级推广佣金比例');
            $table->unsignedDecimal('superior_promotion_commission_upper_limit', 10, 2)->default(0)->comment('上级推广佣金上限（元）');
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
        Schema::dropIfExists('goods_categories');
    }
}
