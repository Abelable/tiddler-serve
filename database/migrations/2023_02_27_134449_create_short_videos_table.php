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
            $table->integer('status')->default(0)->comment('状态：0-待审核，1-审核通过');
            $table->integer('user_id')->comment('作者id');
            $table->string('title')->comment('视频标题');
            $table->string('cover')->comment('封面');
            $table->string('video_url')->comment('视频地址');
            $table->float('longitude')->default(0)->comment('经度');
            $table->float('latitude')->default(0)->comment('纬度');
            $table->string('address')->default('')->comment('具体地址');
            $table->integer('is_private')->default(0)->comment('是否为私密视频：0-否，1-是');
            $table->integer('like_number')->default(0)->comment('点赞数');
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
