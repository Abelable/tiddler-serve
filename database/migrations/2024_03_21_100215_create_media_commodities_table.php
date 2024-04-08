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
            $table->integer('commodity_type')->comment('商品类型：1-景点，2-酒店，3-餐馆，4-商品');
            $table->integer('commodity_id')->comment('商品id');
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
