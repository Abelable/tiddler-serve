<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewYearUserTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_year_user_tasks', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->default(0)->comment('任务状态：0-待完成，1-已完成');
            $table->unsignedBigInteger('user_id')->index()->comment('用户id');
            $table->unsignedBigInteger('task_id')->comment('任务id');
            $table->unsignedBigInteger('reference_id')->nullable()->comment('外部参考ID，如商家id');
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
        Schema::dropIfExists('new_year_user_tasks');
    }
}
