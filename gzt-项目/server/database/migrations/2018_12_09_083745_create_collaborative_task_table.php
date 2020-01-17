<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollaborativeTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collaborative_task', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->longText('description')->comment('协作任务描述');
            $table->json('form_area')->comment('任务相关数据,发起者填写,被邀请者可添加补充内容');
            $table->integer('status')->comment('状态:0是未完成,1是已完成')->default(0);
            $table->integer('initiate_id')->comment('发起者id');
            $table->integer('principal_id')->comment('指定负责人id,若不指定默认为发起者id');
            $table->dateTime('limit_time')->comment('任务期限')->default(null);
            $table->integer('edit_form')->comment('表单编辑,0:都可以编辑,1:仅负责人和发起者可编辑,2:仅协助者可编辑,3:都不能编辑')->default(3);
            $table->integer('difference')->comment('用于在回收站中编辑时区别负责任务还是协助的任务')->default(0);
            $table->integer('is_delete')->comment('放入回收站')->default(0);
            $table->integer('is_receive')->comment('负责人是否接收任务,0未接受,1接收,2拒绝')->default(0);
            $table->integer('form_edit')->comment('是否添加表单')->default(0);
            $table->string('form_people')->comment('可编辑表单的人')->default(null);
            $table->integer('company_id')->comment('公司id')->default(0);
            $table->string('initiate_opinion')->comment('发起人意见')->default(null);
            $table->string('principal_opinion')->comment('负责人意见')->default(null);
            $table->integer('pst_id')->comment('关联评审通的id')->nullable();
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
        Schema::dropIfExists('collaborative_task');
    }
}
