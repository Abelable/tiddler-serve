<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStarTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('star_trips', function (Blueprint $table) {
            $table->id();
            $table->integer('product_type')->comment('产品类型');
            $table->integer('product_id')->comment('产品id');
            $table->string('cover')->comment('封面');
            $table->string('name')->comment('名称');
            $table->string('desc')->comment('描述');
            $table->integer('sort')->default(1)->comment('排序');
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
        Schema::dropIfExists('star_trips');
    }
}
