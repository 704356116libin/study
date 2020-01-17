<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyOssRecord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_oss_record', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id')->comment('企业id')->nullable();
            $table->unsignedInteger('user_id')->comment('个人id')->nullable();
            $table->string(' content')->comment('操作内容');
            $table->string(' type')->comment('操作类型');
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
        Schema::dropIfExists('company_oss_record');
    }
}
