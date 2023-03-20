<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourismNoteCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tourism_note_comments', function (Blueprint $table) {
            $table->id();
            $table->integer('note_id')->comment('攻略笔记id');
            $table->integer('parent_id')->default(0)->comment('回复评论id');
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
        Schema::dropIfExists('tourism_note_comments');
    }
}
