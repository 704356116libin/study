<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('password');
            $table->string('email')->unique()->nullable();
            $table->smallInteger('email_verified')->comment('邮箱验证字段')->default(0);
            $table->smallInteger('current_company_id')->comment('当前所属公司id')->default(0);
            $table->string('tel')->unique();
            $table->smallInteger('tel_verified')->comment('手机验证字段')->default(0);
            $table->string('email_token',40)->unique();
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
        Schema::dropIfExists('users');
    }
}
