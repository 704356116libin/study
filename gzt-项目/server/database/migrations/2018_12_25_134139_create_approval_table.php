<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approval', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('applicant')->comment('该评审申请人id');
            $table->string('name')->comment('审批名称');
            $table->integer('type_id')->comment('类型id');
            $table->json('form_template')->comment('表单模板')->nullable();
            $table->json('process_template')->comment('流程模板')->nullable();
            $table->json('cc_my')->comment('抄送人信息')->nullable();
            $table->string('description')->comment('审批描述')->nullable()->default(null);
            $table->integer('end_status')->comment('该审批最终状态(0进行中1通过或2不通过)')->nullable()->default(0);
            $table->integer('cancel_or_archive')->comment('默认为0.1是撤销,2是归档')->nullable()->default(0);
            $table->string('numbering')->comment('审批编号没有默认null')->nullable()->default(null);
            $table->integer('company_id')->comment('公司id标志');
            $table->dateTime('complete_time')->nullable();
            $table->dateTime('archive_time')->nullable();
            $table->string('approval_method')->comment('流程方式')->nullable()->default('自由流程');
            $table->string('opinion')->comment('意见')->nullable()->default('');
            $table->json('extra_data')->comment('从外部调用审批时需要传递的额外数据')->nullable();
            $table->unsignedInteger('related_pst_id')->comment('相关的评审通id')->index('related_pst_index')->default(0);
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
        Schema::dropIfExists('approval');
    }
}
