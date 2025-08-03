<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLakeCycleMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lake_cycle_media', function (Blueprint $table) {
            $table->id();
            $table->integer('media_type')->comment('媒体类型：1-视频游记，2-图文游记');
            $table->integer('media_id')->comment('媒体id');
            $table->string('cover')->comment('封面');
            $table->string('title')->comment('标题');
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
        Schema::dropIfExists('lake_cycle_media');
    }
}
