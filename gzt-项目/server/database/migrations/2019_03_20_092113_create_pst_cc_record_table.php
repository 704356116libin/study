<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePstCcRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pst_cc_record', function (Blueprint $table) {
           $table->unsignedInteger('pst_id')->comment('评审通id');
           $table->unsignedInteger('user_id')->comment('抄送用户的');
           $table->unsignedInteger('company_id')->comment('哪个公司抄送的');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pst_cc_record');
    }
}
