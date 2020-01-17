<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExternalGroupRelateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_group_relate', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('model_id')->comment('模型id(外部联系人或外部联系公司分组表的id)');
            $table->string('model_type')->comment('模型类型(外部联系人分组表或外部联系公司分组表)');
            $table->integer('external_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('external_group_relate');
    }
}
