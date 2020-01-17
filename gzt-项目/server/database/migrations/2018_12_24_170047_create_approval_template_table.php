<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approval_template', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('模板名称');
            $table->json('form_template')->comment('表单模板');
            $table->json('process_template')->comment('流程模板');
            $table->integer('type_id')->comment('模板的类型id');
            $table->string('approval_method')->comment('审批方式,自由审批和固定审批')->default('自由流程');
            $table->string('description')->comment('审批模板描述')->default(null);
            $table->integer('numbering')->comment('审批编号0是没有,1是有')->default(0);
            $table->integer('company_id')->comment('公司id');
            $table->tinyInteger('is_show')->default(1);
            $table->json('per')->comment('模板可见范围');
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
        Schema::dropIfExists('approval_template');
    }
}
