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
            $table->integer('level')->default(1)->comment('代言人等级');
            $table->integer('scene')->default(100)->comment('场景值，防串改，与等级对应「等级-场景值」：1-100, 2-201, 3-202, 4-203, 5-300');
            $table->integer('path')->comment('生成路径：1-管理后台添加，2-礼包购买');
            $table->string('expiration_time')->comment('身份失效时间');
            $table->integer('order_id')->default(0)->comment('订单id');
            $table->integer('gift_goods_id')->default(0)->comment('礼包商品id-用于售后退款删除代言人身份');
            $table->integer('sub_user_number')->default(0)->comment('下级人数');
            $table->integer('sub_promoter_number')->default(0)->comment('下级代言人人数');
            $table->float('achievement')->default(0)->comment('近三月累计荣誉值');
            $table->float('self_commission_sum')->default(0)->comment('累计自购佣金');
            $table->float('share_commission_sum')->default(0)->comment('累计分享佣金');
            $table->float('team_commission_sum')->default(0)->comment('累计团队佣金');
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
