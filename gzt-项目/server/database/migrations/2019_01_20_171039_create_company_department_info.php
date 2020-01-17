<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyDepartmentInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_department_info', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id')->comment('所属哪个公司')->unique();
            $table->mediumText('info')->comment('公司部门的树信息');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_department_info');
    }
}
