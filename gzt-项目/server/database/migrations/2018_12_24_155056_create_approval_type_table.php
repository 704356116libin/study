<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approval_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('类型名称');
            $table->integer('company_id')->comment('公司id');
            $table->integer('sequence')->comment('排序,根据此字段查找排序,针对于用户拖拽后的排序更新')->default(0);
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
        Schema::dropIfExists('approval_type');
    }
}
