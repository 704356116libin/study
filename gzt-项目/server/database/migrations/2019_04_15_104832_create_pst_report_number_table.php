<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePstReportNumberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pst_report_number', function (Blueprint $table) {
            $table->increments('id')->comment('自增id');
            $table->unsignedInteger('company_id')->comment('所属企业id');
            $table->json('rule_data')->comment('所属企业id');
            $table->integer('current_number',16)->comment('当前文号增长值,初始值');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pst_report_number');
    }
}
