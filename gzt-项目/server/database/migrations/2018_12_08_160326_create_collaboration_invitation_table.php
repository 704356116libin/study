<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollaborationInvitationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collaboration_invitation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('initiate_user')->comment('邀请发起者id');
            $table->integer('receive_user')->comment('邀请接收者id');
            $table->integer('status')->comment('状态:0拒绝,1接受,2完成,3未选择')->default(3);
            $table->integer('collaborative_task_id')->comment('协作任务id');
            $table->integer('difference')->comment('用于在回收站中编辑时区别负责任务还是协助的任务')->default(1);
            $table->integer('is_delete')->comment('放入回收站')->default(0);
            $table->integer('company_id')->comment('公司id')->default(0);
            $table->dateTime('complete_time')->nullable()->comment('参与人完成任务时间');
            $table->string('transfer_reason')->comment('转交理由')->nullable();
            $table->integer('transferred_person')->nullable()->comment('被转交人id');
            $table->integer('replace_company_id')->nullable()->comment('被转交人所属公司的id');
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
        Schema::dropIfExists('collaboration_invitation');
    }
}
