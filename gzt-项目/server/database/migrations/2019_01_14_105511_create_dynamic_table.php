<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDynamicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dynamic', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('所属用户的id')->unique();
            $table->mediumText('list_info')->comment('用户动态列表json数据');
            $table->unsignedInteger('unread_count')->comment('未读数');
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
        Schema::dropIfExists('dynamic');
    }
}
