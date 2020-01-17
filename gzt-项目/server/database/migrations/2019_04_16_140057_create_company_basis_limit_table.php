<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyBasisLimitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_basis_limit', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->string('type',50)->comment('限定字段类型');
            $table->integer('type_number')->comment('限定数量')->default(0);
            $table->integer('user_number')->nullable()->comment('当前使用数量')->default(0);
            $table->timestamp('expire_date')->comment('到期时间');
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
        Schema::dropIfExists('company_basis_limit');
    }
}
