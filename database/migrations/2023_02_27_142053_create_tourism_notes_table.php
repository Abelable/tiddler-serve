<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourismNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tourism_notes', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('作者id');
            $table->longText('image_list')->comment('主图图片列表');
            $table->string('title')->comment('标题');
            $table->longText('content')->comment('内容');
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
        Schema::dropIfExists('tourism_notes');
    }
}
