<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyOssTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_oss', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('company_id');
            $table->string('name',20)->comment('企业云存储')->comment('企业网盘');
            $table->string('root_path',20)->comment('企业云存储根路径');
            $table->double('now_size',16)->comment('企业云存储已使用空间/kb')->default(0);
            $table->double('all_size',16)->comment('企业云存储总空间/kb');
            $table->string('expire_date',50)->comment('磁盘使用到期时间');
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
        Schema::dropIfExists('company_oss');
    }
}
