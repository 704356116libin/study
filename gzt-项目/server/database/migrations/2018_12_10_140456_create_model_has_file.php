<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModelHasFile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_has_file', function (Blueprint $table) {
            $table->unsignedInteger('file_id')->comment('文件id');
            $table->unsignedInteger('model_id')->comment('所属模型的id');
            $table->string('model_type')->comment('所属模型的类名');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('model_has_file');
    }
}
