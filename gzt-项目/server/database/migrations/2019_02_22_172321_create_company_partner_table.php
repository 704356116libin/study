<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyPartnerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_partner', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id')->comment('发送邀请企业的id');
            $table->unsignedInteger('invite_company_id')->comment('目标企业的id');
            $table->integer('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_partner');
    }
}
