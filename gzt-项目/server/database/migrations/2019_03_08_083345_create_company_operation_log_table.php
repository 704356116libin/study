<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyOperationLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_operation_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('module_type')->comment('模块类型');
            $table->string('terminal_equipment')->comment('终端设备');
            $table->string('operation_type')->comment('操作类型');
            $table->integer('operator_id')->comment('操纵人id');
            $table->string('content')->comment('内容');
            $table->integer('company_id');
            $table->dateTime('create_time')->comment('创建时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_operation_log');
    }
}
