<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promoters', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(1)->comment('状态：1-身份正常，2-身份即将失效（续身份窗口期），3-身份失效');
            $table->integer('user_id')->comment('用户id');
            $table->integer('level')->comment('用户等级：1-推广员，2-组织者C1，3-C2，4-C3，5-委员会');
            $table->integer('scene')->comment('场景值，防串改，与等级对应「等级-场景值」：1-100, 2-201, 3-202, 4-203, 5-300');
            $table->integer('path')->comment('生成路径：1-管理后台添加，2-礼包购买，3-限时活动');
            $table->string('gift_goods_ids')->default('')->comment('礼包商品id-用于售后退款删除推广员身份');
            $table->integer('promoted_user_number')->default(0)->comment('推广人数');
            $table->float('self_commission_sum')->default(0)->comment('累计自购佣金');
            $table->float('share_commission_sum')->default(0)->comment('累计分享佣金');
            $table->float('team_commission_sum')->default(0)->comment('累计团队佣金');
            $table->string('expiration_time')->default('')->comment('身份失效时间');
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
        Schema::dropIfExists('promoters');
    }
}
