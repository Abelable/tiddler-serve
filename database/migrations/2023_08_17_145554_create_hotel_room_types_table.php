<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelRoomTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_room_types', function (Blueprint $table) {
            $table->id();
            $table->integer('hotel_id')->comment('酒店id');
            $table->string('name')->comment('酒店房型名称');
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
        Schema::dropIfExists('hotel_room_types');
    }
}
