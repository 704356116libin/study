<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePstTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pst', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('last_pst_id')->comment('上级评审通id')->default(0);
            $table->unsignedInteger('template_id')->comment('采用的评审通模板id');
            $table->unsignedInteger('publish_user_id')->comment('发起人id');
            $table->unsignedInteger('company_id')->comment('所属企业id');
            $table->unsignedInteger('outside_user_id')->comment('外部联系人id')->default(0);
            $table->string('state',20)->comment('评审状态')->index();
            $table->unsignedSmallInteger('need_approval')->comment('相关操作是否需要审批标识');
            $table->unsignedSmallInteger('removed')->comment('是否删除')->default(0);
            $table->json('form_template')->comment('发起评审的表单数据')->nullable();
            $table->json('form_values')->comment('所需要的表单目标k-v')->nullable();
            $table->json('process_template')->comment('所需人员的数据')->nullable();
            $table->string('approval_method')->comment('流程类型:自由流程/固定流程')->nullable();
            $table->json('origin_data')->comment('上级来源数据')->nullable();
            $table->json('join_user_data')->comment('参与人员的数据')->nullable();
            $table->json('join_pst_form_data')->comment('内部参与人提交的表单数据信息')->nullable();
            $table->json('transfer_join_data')->comment('参与人转移数据json')->nullable();
            $table->json('cc_user_data')->comment('抄送人员的数据')->nullable();
            $table->json('duty_user_data')->comment('负责人信息json')->nullable();
            $table->json('transfer_duty_data')->comment('转移负责人json')->nullable();
            $table->unsignedInteger('last_duty_user_id')->comment('上一环负责人id')->default(0);
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
        Schema::dropIfExists('pst');
    }
}
