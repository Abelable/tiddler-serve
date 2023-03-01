<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('作者id');
            $table->integer('type')->comment('媒体类型：1-直播（只存直播、预告），2-短视频，3-短图文（攻略）');
            $table->integer('media_id')->comment('媒体id');
            $table->integer('viewers_number')->default(0)->comment('观看人数');
            $table->integer('praise_number')->default(0)->comment('点赞数');
            $table->integer('comments_number')->default(0)->comment('评论数');
            $table->integer('collection_times')->default(0)->comment('收藏次数');
            $table->integer('share_times')->default(0)->comment('分享次数');
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
        Schema::dropIfExists('media');
    }
}
