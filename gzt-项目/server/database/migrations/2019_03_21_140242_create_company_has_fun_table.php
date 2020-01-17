<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyHasFunTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_has_fun', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('per_sort_id')->comment('公司拥有功能模块的id');
            $table->integer('company_id');
            $table->integer('is_enable')->default(1)->comment('功能是否启用默认1为启用0禁用');
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
        Schema::dropIfExists('company_has_fun');
    }
}
