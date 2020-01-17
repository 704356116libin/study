<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePstFormDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pst_form_data', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id')->comment('所属企业id');
            $table->json('service_department')->comment('送审业务负责科室标签json');
            $table->json('action_label')->comment('行为标签json数据');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pst_form_data');
    }
}
