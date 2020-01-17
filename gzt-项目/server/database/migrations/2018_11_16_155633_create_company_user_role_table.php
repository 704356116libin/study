<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyUserRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_user_role', function (Blueprint $table) {
            $table->integer('company_id')->comment('公司/组织id');
            $table->integer('user_id')->comment('用户id');
            $table->integer('role_id')->comment('角色/职务id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_user_role');
    }
}
