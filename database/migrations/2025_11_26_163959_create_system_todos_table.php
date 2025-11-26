<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemTodosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_todos', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(0)->comment('状态：0-待处理，1-已处理');
            $table->integer('type')->comment('类型');
            $table->string('reference_id')->default('')->comment('外部参考ID，如订单ID');
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
        Schema::dropIfExists('system_todos');
    }
}
