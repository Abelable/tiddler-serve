<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoterQasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promoter_qas', function (Blueprint $table) {
            $table->id();
            $table->integer('promoter_id')->comment('代言人id');
            $table->integer('user_id')->comment('用户id');
            $table->string('question')->comment('问题');
            $table->string('answer')->default('')->comment('答案');
            $table->string('answer_time')->default('')->comment('回答时间');
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
        Schema::dropIfExists('promoter_qas');
    }
}
