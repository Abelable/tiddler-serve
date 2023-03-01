<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShortVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('short_videos', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('作者id');
            $table->string('cover')->default('')->comment('封面');
            $table->string('video_url')->comment('视频地址');
            $table->string('title')->comment('视频标题');
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
        Schema::dropIfExists('short_videos');
    }
}
