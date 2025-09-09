<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderSetMealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_set_meals', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户id');
            $table->integer('order_id')->comment('订单id');
            $table->integer('restaurant_id')->comment('餐厅id');
            $table->string('restaurant_cover')->default('')->comment('餐厅封面');
            $table->string('restaurant_name')->comment('餐厅名称');
            $table->integer('set_meal_id')->comment('套餐id');
            $table->string('cover')->comment('套餐图片');
            $table->string('name')->comment('套餐名称');
            $table->float('price')->comment('套餐售价');
            $table->float('original_price')->comment('套餐抵扣价格');
            $table->integer('number')->comment('套餐数量');
            $table->float('sales_commission_rate')->comment('销售佣金比例');
            $table->float('promotion_commission_rate')->comment('推广佣金比例%');
            $table->float('promotion_commission_upper_limit')->comment('推广佣金上限');
            $table->float('superior_promotion_commission_rate')->comment('上级推广佣金比例%');
            $table->float('superior_promotion_commission_upper_limit')->comment('上级推广佣金上限');
            $table->longText('package_details')->comment('套餐详情');
            $table->integer('validity_days')->default(0)->comment('有效天数');
            $table->string('validity_start_time')->default('')->comment('范围有效期开始时间');
            $table->string('validity_end_time')->default('')->comment('范围有效期结束时间');
            $table->integer('buy_limit')->default(0)->comment('限购数量');
            $table->integer('per_table_usage_limit')->default(0)->comment('单桌使用数量限制');
            $table->longText('use_time_list')->comment('使用时间范围');
            $table->integer('need_pre_book')->default(0)->comment('是否需要预定：0-不需要预定，1-需要预定');
            $table->longText('use_rules')->comment('使用规则');
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
        Schema::dropIfExists('order_set_meals');
    }
}
