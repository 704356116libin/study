<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyPartnerRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_partner_record', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id')->comment('企业id');
            $table->unsignedInteger('invite_company_id')->comment('被邀请企业id');
            $table->unsignedInteger('operate_user_id')->comment('操作人id');
            $table->string('invite_company_name')->comment('被邀请企业名称');
            $table->unsignedSmallInteger('state')->comment('邀请的状态')->default(2);
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
        Schema::dropIfExists('company_partner_record');
    }
}
