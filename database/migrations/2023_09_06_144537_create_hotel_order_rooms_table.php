<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelOrderRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_order_rooms', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户id');
            $table->integer('order_id')->comment('订单id');
            $table->integer('hotel_id')->comment('酒店id');
            $table->string('hotel_name')->comment('酒店名称');
            $table->integer('room_id')->comment('房间id');
            $table->integer('type_id')->comment('房间类型id');
            $table->string('type_name')->comment('房间类型名称');
            $table->string('check_in_date')->comment('入住时间');
            $table->string('check_out_date')->comment('退房时间');
            $table->float('price')->comment('房间价格');
            $table->float('sales_commission_rate')->comment('销售佣金比例');
            $table->float('promotion_commission_rate')->comment('推广佣金比例%');
            $table->float('promotion_commission_upper_limit')->comment('推广佣金上限');
            $table->float('superior_promotion_commission_rate')->comment('上级推广佣金比例%');
            $table->float('superior_promotion_commission_upper_limit')->comment('上级推广佣金上限');
            $table->integer('number')->comment('房间数量');
            $table->integer('breakfast_num')->comment('早餐数量');
            $table->integer('guest_num')->comment('入住人数');
            $table->integer('cancellable')->comment('免费取消：0-不可取消，1-可免费取消');
            $table->longText('image_list')->comment('房间照片');
            $table->string('bed_desc')->comment('床铺描述');
            $table->float('area_size')->comment('房间面积');
            $table->string('floor_desc')->comment('楼层描述');
            $table->longText('facility_list')->comment('房间设施');
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
        Schema::dropIfExists('hotel_order_rooms');
    }
}
