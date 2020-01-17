<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePstOperateRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pst_operate_record', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pst_id')->comment('评审通id');
            $table->unsignedInteger('company_id')->comment('所属企业的id');
            $table->string('type')->comment('操作标识');
            $table->unsignedInteger('operate_user_id')->comment('操作用户id');
            $table->unsignedInteger('operate_name')->comment('操作用户name');
            $table->text('info')->comment('操作详情信息');
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
        Schema::dropIfExists('pst_operate_record');
    }
}
