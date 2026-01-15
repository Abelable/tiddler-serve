<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewYearGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_year_goods', function (Blueprint $table) {
            $table->id();

            $table->integer('goods_id')->comment('商品id');
            $table->string('cover')->comment('图片');
            $table->string('name')->comment('名称');
            $table->integer('luck_score')->comment('兑换所需福气值');
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
        Schema::dropIfExists('new_year_goods');
    }
}
