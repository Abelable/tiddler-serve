<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complaint_options', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->comment('类型：1-景点，2-酒店，3-餐饮门店，4-商品，5-代言人');
            $table->string('title')->comment('标题');
            $table->string('content')->default('')->comment('内容');
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
        Schema::dropIfExists('complaint_options');
    }
}
