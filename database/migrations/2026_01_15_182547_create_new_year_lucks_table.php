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
            $table->unsignedBigInteger('user_id')->comment('用户id');
            $table->unsignedBigInteger('task_id')->default(0)->comment('任务id');
            $table->date('task_date')->comment('任务日期（用于唯一约束）');

            $table->string('desc', 200)->comment('描述');
            $table->tinyInteger('type')->comment('类型：1-获取，2-消耗');
            $table->integer('score')->comment('福气值');

            $table->timestamps();
            $table->softDeletes();

            // 索引
            $table->index('user_id');
            $table->unique(
                ['user_id', 'task_id', 'task_date'],
                'uniq_user_task_date'
            );
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
