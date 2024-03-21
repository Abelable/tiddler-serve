<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaCommoditiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_commodities', function (Blueprint $table) {
            $table->id();
            $table->integer('media_type')->comment('媒体类型：1-短视频，2-图文游记');
            $table->integer('media_id')->comment('媒体id');
            $table->integer('scenic_id')->default(0)->comment('媒体关联酒店id');
            $table->integer('hotel_id')->default(0)->comment('媒体关联酒店id');
            $table->integer('restaurant_id')->default(0)->comment('媒体关联餐馆id');
            $table->integer('goods_id')->default(0)->comment('媒体关联商品id');
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
        Schema::dropIfExists('media_commodities');
    }
}
