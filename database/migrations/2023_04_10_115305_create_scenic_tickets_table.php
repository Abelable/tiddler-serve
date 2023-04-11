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
            $table->integer('user_id')->comment('用户id');
            $table->integer('provider_id')->comment('供应商id');
            $table->string('scenic_ids')->comment('景点id列表');
            $table->integer('type')->default(1)->comment('门票类型：1-单景点门票，2-多景点联票');
            $table->integer('status')->default(0)->comment('申请状态：0-待审核，1-审核通过，2-审核未通过，3-下架');
            $table->string('failure_reason')->default('')->comment('审核失败原因');
            $table->string('name')->comment('门票名称');
            $table->float('price')->comment('门票最低价格');
            $table->float('market_price')->default(0)->comment('门票市场价格');
            $table->integer('stock')->comment('门票总库存');
            $table->float('sales_commission_rate')->default(0.1)->comment('销售佣金比例');
            $table->float('promotion_commission_rate')->default(0.02)->comment('推广佣金比例');
            $table->integer('sales_volume')->default(0)->comment('门票销量');
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
