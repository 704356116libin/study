<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonalOssFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personal_oss_file', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uploader_id')->comment('上传者id');
            $table->integer('user_id')->comment('所属用户的id');
            $table->string('name')->comment('项目中文件名显示(原名)');
            $table->float('size')->comment('文件大小/kb');
            $table->string('oss_path')->comment('对应的阿里云路径');
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
        Schema::dropIfExists('personal_oss_file');
    }
}
