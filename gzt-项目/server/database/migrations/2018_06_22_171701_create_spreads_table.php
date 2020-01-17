<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spreads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('对应用户');
            $table->string('path',20)->comment('邀请链接');
            $table->integer('num')->commment('邀请人数')->default(0);
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
        Schema::dropIfExists('spreads');
    }
}
