<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScenicOrderTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scenic_order_tickets', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户id');
            $table->integer('order_id')->comment('订单id');
            $table->integer('ticket_id')->comment('门票id');
            $table->string('name')->comment('门票名称');
            $table->integer('category_id')->comment('门票分类id');
            $table->string('category_name')->comment('门票分类');
            $table->string('selected_date_timestamp')->comment('选中日期时间戳');
            $table->float('price')->comment('门票价格');
            $table->float('sales_commission_rate')->comment('销售佣金比例');
            $table->float('promotion_commission_rate')->comment('推广佣金比例%');
            $table->float('promotion_commission_upper_limit')->comment('推广佣金上限');
            $table->float('superior_promotion_commission_rate')->comment('上级推广佣金比例%');
            $table->float('superior_promotion_commission_upper_limit')->comment('上级推广佣金上限');
            $table->integer('number')->comment('门票数量');
            $table->integer('effective_time')->comment('生效时间，单位小时');
            $table->integer('validity_time')->comment('有效期, 单位天');
            $table->integer('refund_status')->comment('退票状态：1-随时可退，2-有条件退，3-不可退');
            $table->integer('need_exchange')->comment('是否需要换票：0-无需换票，1-需要换票');
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
        Schema::dropIfExists('scenic_order_tickets');
    }
}
