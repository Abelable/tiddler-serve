<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoterEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promoter_evaluations', function (Blueprint $table) {
            $table->id();
            $table->integer('promoter_id')->comment('代言人id');
            $table->integer('user_id')->comment('用户id');
            $table->float('score')->comment('评分');
            $table->string('content')->comment('内容');
            $table->string('image_list')->default('[]')->comment('图片');
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
        Schema::dropIfExists('promoter_evaluations');
    }
}
