<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserOssTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_oss', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('user_id');
            $table->string('name',20)->comment('个人云存储')->default('个人网盘');
            $table->string('root_path',20)->comment('个人云存储根路径');
            $table->unsignedBigInteger('now_size')->comment('个人云存储已使用空间/kb')->default(0);
            $table->unsignedBigInteger('all_size')->comment('个人云存储总空间/kb')->default(config('oss.user.default_size'));
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
        Schema::dropIfExists('user_oss');
    }
}
