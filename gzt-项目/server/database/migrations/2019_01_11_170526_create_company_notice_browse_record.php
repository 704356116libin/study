<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyNoticeBrowseRecord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_notice_browse_record', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->comment('浏览用户');
            $table->unsignedInteger('notice_id')->comment('浏览的公告');
            $table->string('info')->comment('浏览用户的一些信息');
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
        Schema::dropIfExists('company_notice_browse_record');
    }
}
