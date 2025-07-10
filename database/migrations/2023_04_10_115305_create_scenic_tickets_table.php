<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScenicTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scenic_tickets', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id')->comment('店铺id');
            $table->integer('type')->comment('门票类型：1-单景点门票，2-多景点联票');
            $table->integer('status')->default(0)->comment('申请状态：0-待审核，1-审核通过，2-审核未通过，3-下架');
            $table->string('failure_reason')->default('')->comment('审核失败原因');
            $table->string('name')->comment('门票名称');
            $table->string('brief_name')->comment('门票简称');
            $table->float('price')->comment('门票最低价格');
            $table->float('market_price')->default(0)->comment('门票市场价格');
            $table->float('sales_commission_rate')->default(0)->comment('销售佣金比例%');
            $table->float('promotion_commission_rate')->default(0)->comment('推广佣金比例%');
            $table->float('promotion_commission_upper_limit')->default(0)->comment('推广佣金上限');
            $table->float('superior_promotion_commission_rate')->default(0)->comment('上级推广佣金比例%');
            $table->float('superior_promotion_commission_upper_limit')->default(0)->comment('上级推广佣金上限');
            $table->integer('sales_volume')->default(0)->comment('门票销量');
            $table->string('fee_include_tips')->default('')->comment('费用包含说明');
            $table->string('fee_not_include_tips')->default('')->comment('费用不含说明');
            $table->string('booking_time')->comment('当天预定最晚时间');
            $table->integer('effective_time')->default(0)->comment('生效时间，单位小时');
            $table->integer('validity_time')->default(0)->comment('有效期, 单位天');
            $table->integer('limit_number')->default(0)->comment('限购数量');
            $table->integer('refund_status')->comment('退票状态：1-随时可退，2-有条件退，3-不可退');
            $table->string('refund_tips')->default('')->comment('退票说明');
            $table->integer('need_exchange')->comment('是否需要换票：0-无需换票，1-需要换票');
            $table->string('exchange_tips')->default('')->comment('换票说明');
            $table->string('exchange_time')->default('')->comment('换票时间范围');
            $table->string('exchange_location')->default('')->comment('换票地点');
            $table->string('enter_time')->default('')->comment('入园时间范围');
            $table->string('enter_location')->default('')->comment('入园地点');
            $table->string('invoice_tips')->default('')->comment('发票说明');
            $table->string('reminder_tips')->default('')->comment('特别提醒');
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
        Schema::dropIfExists('scenic_tickets');
    }
}
