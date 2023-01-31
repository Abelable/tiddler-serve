<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreightTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('freight_templates', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户id');
            $table->integer('mode')->default(1)->comment('模板类型：1-自定义模板，2-快递模板');
            $table->string('name')->comment('模板名称');
            $table->string('title')->comment('模板标题，可展示在商品详情页');
            $table->integer('compute_mode')->default(1)->comment('计算方式：1-不计重量和件数，2-按商品件数');
            $table->float('free_quota')->default(0)->comment('免费额度');
            $table->longText('area_list')->comment('自定义模板的配送地区列表');
            $table->longText('express_list')->comment('自定义模板的快递方式列表');
            $table->longText('express_template_lists')->comment('快递模板列表');
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
        Schema::dropIfExists('freight_templates');
    }
}
