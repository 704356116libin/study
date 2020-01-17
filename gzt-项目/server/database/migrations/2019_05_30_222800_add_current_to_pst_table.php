<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCurrentToPstTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pst', function (Blueprint $table) {
            $table->json('current_handlers')->after('need_approval')->comment('当前处理企业id/个人id')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pst', function (Blueprint $table) {
           $table->dropColumn('current_handlers');
        });
    }
}
