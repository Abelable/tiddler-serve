<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopTodosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_todos', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id')->comment('店铺id');
            $table->integer('status')->default(0)->comment('状态：1-待处理，2-已处理');
            $table->integer('type')->comment('类型：1-待发货，2-售后');
            $table->string('reference_id')->default('')->comment('外部参考ID，如订单ID');
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
        Schema::dropIfExists('shop_todos');
    }
}
