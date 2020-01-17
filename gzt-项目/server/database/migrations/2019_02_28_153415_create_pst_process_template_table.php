<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePstProcessTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pst_process_template', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id')->comment('所属企业的id');
            $table->unsignedInteger('process_type_id')->comment('流程所属类型id');
            $table->unsignedSmallInteger('is_show')->comment('是否启用')->default(1);
            $table->string('name')->comment('流程模板名称')->default('');
            $table->json('process_template')->comment('流程模板数据');
            $table->json('per')->comment('可见人相关信息');
            $table->string('description')->comment('流程描述')->default('');
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
        Schema::dropIfExists('pst_process_template');
    }
}
