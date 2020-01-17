<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnterpriseCertificationInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enterprise_certification_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('abbreviation')->comment('公司简称');
            $table->string('number')->comment('企业号');
            $table->integer('logo_id')->comment('公司logo 的id');
            $table->string('tel')->comment('企业电话');
            $table->string('type')->comment('企业类型');
            $table->json('district')->comment('所属地区');
            $table->json('industry')->comment('所属行业');
            $table->string('address')->comment('公司地址');
            $table->integer('zip_code')->comment('邮编');
            $table->string('fax')->comment('传真');
            $table->string('url')->comment('公司网址');
            $table->integer('company_id')->comment('认证公司id');
            $table->integer('license_id')->comment('执照文件id');
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
        Schema::dropIfExists('enterprise_certification_info');
    }
}
