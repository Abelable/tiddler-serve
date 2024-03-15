<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_evaluations', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户id');
            $table->integer('hotel_id')->comment('酒店id');
            $table->float('score')->comment('酒店评分');
            $table->string('content')->comment('评论内容');
            $table->longText('image_list')->comment('评论图片');
            $table->integer('like_number')->default(0)->comment('点赞数');
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
        Schema::dropIfExists('hotel_evaluations');
    }
}
