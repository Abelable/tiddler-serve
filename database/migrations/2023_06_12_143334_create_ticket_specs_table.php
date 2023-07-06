<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketSpecsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_specs', function (Blueprint $table) {
            $table->id();
            $table->integer('ticket_id')->comment('门票id');
            $table->integer('category_id')->comment('门票分类id');
            $table->string('price_list')->comment('价格列表：分时间段设置价格及对应库存');
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
        Schema::dropIfExists('ticket_specs');
    }
}
