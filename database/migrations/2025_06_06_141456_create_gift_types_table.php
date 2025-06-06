<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gift_types', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(1)->comment('状态: 1-显示,2-隐藏');
            $table->string('name')->comment('礼包类型名称');
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
        Schema::dropIfExists('gift_types');
    }
}
