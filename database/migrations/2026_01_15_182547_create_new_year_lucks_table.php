<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewYearLucksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_year_lucks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->comment('用户id');

            $table->string('desc', 200)->comment('描述');
            $table->tinyInteger('type')->comment('类型：1-获取，2-消耗');
            $table->integer('score')->comment('福气值');
            $table->unsignedBigInteger('task_id')->default(0)->comment('任务id');

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
        Schema::dropIfExists('new_year_lucks');
    }
}
