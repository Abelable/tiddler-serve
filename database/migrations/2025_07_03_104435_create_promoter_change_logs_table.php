<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoterChangeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promoter_change_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('promoter_id')->comment('代言人id');
            $table->integer('change_type')->comment('变更类型：1-身份升级，2-有效期变更');
            $table->integer('old_level')->default(0)->comment('旧等级');
            $table->integer('new_level')->default(0)->comment('新等级');
            $table->string('old_expiration_time')->default('')->comment('旧失效时间');
            $table->string('new_expiration_time')->default('')->comment('新失效时间');
            $table->integer('old_gift_goods_id')->default(0)->comment('旧家乡好物id');
            $table->integer('new_gift_goods_id')->default(0)->comment('新家乡好物id');
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
        Schema::dropIfExists('promoter_change_logs');
    }
}
