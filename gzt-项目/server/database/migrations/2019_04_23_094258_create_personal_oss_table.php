<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonalOssTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personal_oss', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('user_id');
            $table->string('name',20)->comment('个人云存储')->comment('个人网盘');
            $table->string('root_path',20)->comment('个人云存储根路径');
            $table->double('now_size',16)->comment('个人云存储已使用空间/kb')->default(0);
            $table->double('all_size',16)->comment('个人云存储总空间/kb');
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
        Schema::dropIfExists('personal_oss');
    }
}
