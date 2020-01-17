<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approval_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('approval_id')->comment('审批id');
            $table->integer('approver_id')->comment('审批人id');
            $table->string('approval_method')->comment('审批方式');
            $table->integer('approval_level')->comment('审批级数,用于确定该审批人是处于第几级,(查找数据是用于排序)');
            $table->string('type')->comment('会签???');
            $table->integer('status')->comment('我的这一级审批状态(通过or不通过)0是未收到,1是审核中,2通过,3不通过')->default(0);
            $table->integer('level_status')->comment('这是等级的状态0审批中,1审批结束')->default(0);
            $table->date('level_end_time')->comment('该等级的结束时间')->default(null);
            $table->string('opinion')->comment('审批意见')->default(null);
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
        Schema::dropIfExists('approval_user');
    }
}
