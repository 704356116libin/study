<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCompanyInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_company_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('company_id');
            $table->string('name');
            $table->string('sex')->nullable();
            $table->string('tel');
            $table->string('email')->nullable();
            $table->json('role_ids')->nullable();
            $table->string('remark')->nullable();
            $table->string('address')->nullable();
            $table->string('room_number')->nullable();
            $table->integer('activation')->default(0)->comment('0为未激活状态,1为激活状态');
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
        Schema::dropIfExists('user_company_info');
    }
}
