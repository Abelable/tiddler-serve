<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewYearPrizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_year_prizes', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(1)->comment('状态：1-上架中；2-已下架');

            $table->tinyInteger('type')->comment('类型：1-福气值，2-优惠券，3-商品');
            $table->unsignedBigInteger('coupon_id')->default(0)->comment('优惠券id');
            $table->unsignedBigInteger('goods_id')->default(0)->comment('商品id');
            $table->tinyInteger('is_big')->default(0)->comment('是否是大奖：0-否，1-是');

            $table->string('cover', 500)->comment('奖品图片');
            $table->string('name', 100)->comment('奖品名称');

            $table->decimal('rate', 8, 6)->default(0)->comment('抽奖概率，0~1');
            $table->integer('stock')->default(-1)->comment('库存：-1不限，0售罄');
            $table->integer('luck_score')->default(0)->comment('福气值数量，仅 type=1 有效');
            $table->decimal('cost', 8, 2)->default(0)->comment('单次命中真实成本');

            $table->integer('limit_per_user')->default(0)->comment('单用户最多命中次数，0不限');
            $table->timestamp('start_at')->nullable()->comment('生效开始时间');
            $table->timestamp('end_at')->nullable()->comment('生效结束时间');
            $table->unsignedBigInteger('fallback_prize_id')->default(0)->comment('库存不足降级奖品');

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
        Schema::dropIfExists('new_year_prizes');
    }
}
