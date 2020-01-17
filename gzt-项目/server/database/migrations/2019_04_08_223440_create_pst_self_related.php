<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePstSelfRelated extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pst_self_related', function (Blueprint $table) {
            $table->unsignedInteger('target_pst_id')->comment('目标评审通id');
            $table->unsignedInteger('related_pst_id')->comment('所关联的评审通id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pst_self_related');
    }
}
