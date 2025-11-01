<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_tags', function (Blueprint $table) {
            $table->id();
            $table->integer('scene')->comment('使用场景：1-景点，2-酒店，3-餐饮门店，4-商品，5-代言人');
            $table->integer('type')->comment('类型：1-正向标签（好评类），2-中性标签（一般类），3-负向标签（差评类）');
            $table->string('content')->comment('内容');
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
        Schema::dropIfExists('evaluation_tags');
    }
}
