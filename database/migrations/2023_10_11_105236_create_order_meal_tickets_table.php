<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderMealTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_meal_tickets', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->comment('订单id');
            $table->integer('number')->comment('代金券数量');
            $table->integer('ticket_id')->comment('代金券id');
            $table->float('price')->comment('代金券售价');
            $table->float('original_price')->comment('代金券抵扣价格');
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
        Schema::dropIfExists('order_meal_tickets');
    }
}
