<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMealTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meal_tickets', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(0)->comment('申请状态：0-待审核，1-审核通过，2-审核未通过，3-下架');
            $table->string('failure_reason')->default('')->comment('审核失败原因');
            $table->integer('shop_id')->comment('店铺id');
            $table->float('price')->comment('代金券价格');
            $table->float('original_price')->comment('抵扣原价');
            $table->float('sales_commission_rate')->default(0)->comment('销售佣金比例%');
            $table->float('promotion_commission_rate')->default(0)->comment('推广佣金比例%');
            $table->float('promotion_commission_upper_limit')->default(0)->comment('推广佣金上限');
            $table->float('superior_promotion_commission_rate')->default(0)->comment('上级推广佣金比例%');
            $table->float('superior_promotion_commission_upper_limit')->default(0)->comment('上级推广佣金上限');
            $table->integer('sales_volume')->default(0)->comment('代金券销量');
            $table->integer('validity_days')->default(0)->comment('有效天数');
            $table->string('validity_start_time')->default('')->comment('范围有效期开始时间');
            $table->string('validity_end_time')->default('')->comment('范围有效期结束时间');
            $table->integer('buy_limit')->default(0)->comment('限购数量');
            $table->integer('per_table_usage_limit')->default(0)->comment('单桌使用数量限制');
            $table->integer('overlay_usage_limit')->default(0)->comment('叠加使用数量限制');
            $table->longText('use_time_list')->comment('使用时间范围');
            $table->longText('inapplicable_products')->comment('不可用商品列表');
            $table->integer('box_available')->default(0)->comment('包厢是否可用：0-不可用，1-可用');
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
        Schema::dropIfExists('meal_tickets');
    }
}
