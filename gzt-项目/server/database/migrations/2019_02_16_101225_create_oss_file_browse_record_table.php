<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOssFileBrowseRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create ('oss_file_browse_record', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->comment('浏览用户id');
            $table->unsignedInteger('file_id')->comment('浏览的文件id');
            $table->string('name')->comment('浏览用户的名字');
            $table->string('type')->comment('文件操作标识');
            $table->timestamp('time')->comment('浏览的时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oss_file_browse_record');
    }
}
