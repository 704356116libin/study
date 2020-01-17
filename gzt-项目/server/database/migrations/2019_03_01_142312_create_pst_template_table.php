<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePstTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pst_template', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('评审通模板名称')->default('');
            $table->unsignedInteger('type_id')->comment('对应的评审通模板类型id');
            $table->unsignedInteger('company_id')->comment('所属公司id');
            $table->unsignedSmallInteger('is_show')->comment('是否启用')->default(1);
            $table->unsignedSmallInteger('need_approval')->comment('相关操作是否需要审批');
            $table->json('form_template')->comment('该模板的表单数据');
            $table->json('form_values')->comment('该模板所需要的数据 k-v数组')->nullable();
            $table->json('process_template')->comment('审批人员流程数据')->nullable();
            $table->string('approval_method')->comment('流程类型:自由流程/固定流程')->default('');
            $table->json('cc_users')->comment('抄送人员源数据');
            $table->json('per')->comment('可见人员源数据');
            $table->json('users_info')->comment('抄送，可见，....相关人员json数据');
            $table->string('description')->comment('描述信息')->default('');
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
        Schema::dropIfExists('pst_template');
    }
}
