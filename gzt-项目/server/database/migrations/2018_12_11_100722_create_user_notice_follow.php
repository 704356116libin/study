<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserNoticeFollow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_notice_follow', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->comment('关注用户的id');
            $table->unsignedInteger('notice_id')->comment('所关注的notice_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_notice_follow');
    }
}
