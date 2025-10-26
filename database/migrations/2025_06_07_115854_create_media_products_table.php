<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_products', function (Blueprint $table) {
            $table->id();
            $table->integer('media_type')->comment('媒体类型：1-视频游记，2-图文游记');
            $table->integer('media_id')->comment('媒体id');
            $table->integer('product_type')->comment('商品类型：1-景点，2-酒店，3-餐馆，4-商品');
            $table->integer('product_id')->comment('商品id');
            $table->integer('sort')->default(1)->comment('排序');
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
        Schema::dropIfExists('media_products');
    }
}
