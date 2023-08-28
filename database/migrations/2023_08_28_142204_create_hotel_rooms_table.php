<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户id');
            $table->integer('provider_id')->comment('供应商id');
            $table->integer('shop_id')->comment('店铺id');
            $table->integer('hotel_id')->comment('酒店id');
            $table->integer('type_id')->comment('房间类型id');
            $table->integer('status')->default(0)->comment('申请状态：0-待审核，1-审核通过，2-审核未通过，3-下架');
            $table->string('failure_reason')->default('')->comment('审核失败原因');
            $table->float('sales_commission_rate')->comment('销售佣金比例');
            $table->float('promotion_commission_rate')->comment('推广佣金比例');
            $table->integer('breakfast_num')->comment('早餐份数');
            $table->integer('guest_num')->comment('可入住客人数量');
            $table->integer('cancellable')->comment('免费取消：0-不可取消，1-可免费取消');
            $table->float('price')->comment('房间起始价格');
            $table->integer('sales_volume')->default(0)->comment('房间销量');
            $table->longText('price_list')->comment('价格列表：分时间段设置价格');
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
        Schema::dropIfExists('hotel_rooms');
    }
}
