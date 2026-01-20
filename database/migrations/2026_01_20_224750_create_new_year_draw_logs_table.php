<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewYearDrawLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_year_draw_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->comment('用户id');

            $table->unsignedBigInteger('prize_id')->nullable()->comment('奖品id，未中奖可为空');
            $table->tinyInteger('prize_type')->default(0)->comment('奖品类型：0-谢谢参与，1-福气值，2-优惠券，3-商品');
            $table->string('prize_cover', 500)->default('')->comment('奖品图片');
            $table->string('prize_name', 255)->default('谢谢参与')->comment('奖品名称');
            $table->integer('prize_cost')->default(0)->comment('对应奖品成本/福气值/优惠券面值，未中奖为0');

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
        Schema::dropIfExists('new_year_draw_logs');
    }
}
