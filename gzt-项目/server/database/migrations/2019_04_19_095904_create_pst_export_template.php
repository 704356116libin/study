<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePstExportTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pst_export_template', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('评审通导出模板名称')->default('');
            $table->unsignedInteger('type_id')->comment('对应的评审通导出模板类型id');
            $table->unsignedInteger('company_id')->comment('所属公司id');
            $table->unsignedSmallInteger('is_show')->comment('是否启用')->default(1);
            $table->string('header',255)->comment('页眉数据')->nullable();
            $table->string('footer',255)->comment('页尾数据')->nullable();
            $table->json('text')->comment('正文数据');
            $table->json('parameter')->comment('参数说明书')->nullable();
            $table->json('per')->comment('可见人员源数据,默认全体可见')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pst_export_template');
    }
}
