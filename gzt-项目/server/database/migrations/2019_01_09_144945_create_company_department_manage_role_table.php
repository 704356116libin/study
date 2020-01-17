<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyDepartmentManageRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_department_manage_role', function (Blueprint $table) {
            $table->integer('department_id')->comment('部门id');
            $table->integer('role_id')->comment('拥有管理权的角色id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_department_manage_role');
    }
}
