<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoterEvaluationTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promoter_evaluation_tags', function (Blueprint $table) {
            $table->id();
            $table->integer('promoter_id')->comment('代言人id');
            $table->integer('tag_id')->comment('标签id');
            $table->integer('evaluation_id')->comment('评论id');
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
        Schema::dropIfExists('promoter_evaluation_tags');
    }
}
