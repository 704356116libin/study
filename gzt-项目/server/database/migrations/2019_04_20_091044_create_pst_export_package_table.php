<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePstExportPackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pst_export_package', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',60);
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('company_id');
            $table->json('export_template')->nullable();
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
        Schema::dropIfExists('pst_export_package');
    }
}
