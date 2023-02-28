<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShortVideoCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('short_video_comments', function (Blueprint $table) {
            $table->id();
            $table->integer('comment_id')->default(0)->comment('回复评论id');
            $table->integer('user_id')->comment('用户id');
            $table->string('content')->comment('评论内容');
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
        Schema::dropIfExists('short_video_comments');
    }
}
