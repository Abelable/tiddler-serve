<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLakeHomestaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lake_homestays', function (Blueprint $table) {
            $table->id();
            $table->integer('hotel_id')->comment('民宿id');
            $table->string('cover')->comment('民宿封面');
            $table->string('name')->comment('民宿名称');
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
        Schema::dropIfExists('lake_homestays');
    }
}
