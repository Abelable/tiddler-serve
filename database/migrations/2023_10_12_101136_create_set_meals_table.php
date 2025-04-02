<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSetMealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('set_meals', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户id');
            $table->integer('provider_id')->comment('供应商id');
            $table->integer('status')->default(0)->comment('申请状态：0-待审核，1-审核通过，2-审核未通过，3-下架');
            $table->string('failure_reason')->default('')->comment('审核失败原因');
            $table->string('cover')->comment('套餐图片');
            $table->string('name')->comment('套餐名称');
            $table->float('price')->comment('套餐价格');
            $table->float('original_price')->comment('套餐原价');
            $table->float('sales_commission_rate')->default(0)->comment('销售佣金比例');
            $table->float('promotion_commission_rate')->default(0)->comment('推广佣金比例');
            $table->float('promotion_commission_upper_limit')->default(0)->comment('推广佣金上限');
            $table->integer('sales_volume')->default(0)->comment('销量');
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
        Schema::dropIfExists('set_meals');
    }
}
