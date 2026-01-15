<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewYearTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_year_tasks', function (Blueprint $table) {
            $table->id();

            $table->string('icon', 500)->comment('任务图标');
            $table->string('name', 200)->comment('任务名称');
            $table->string('desc', 200)->comment('任务描述');
            $table->string('btn_content', 200)->comment('按钮内容');

            $table->integer('luck_score')->comment('任务福气值');

            $table->tinyInteger('type')->comment('任务类型：1-页面跳转, 2-加群');
            $table->string('param')->comment('任务参数，例如页面路径');

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
        Schema::dropIfExists('new_year_tasks');
    }
}
