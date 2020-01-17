<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',100)->comment('公司名称');
            $table->string('creator_id',20)->comment('公司创建者的id');
            $table->smallInteger('verified')->comment('公司认证标识')->nullable()->default(0);
            $table->integer('email_count')->comment('可用邮件条数');
            $table->integer('sms_count')->comment('可用短信条数');
            $table->string('abbreviation')->comment('公司简称')->nullable();
            $table->string('number')->comment('企业号')->nullable()->default('000000');
            $table->integer('logo_id')->comment('公司logo 的id')->nullable();
            $table->string('tel')->comment('企业电话')->nullable();
            $table->string('type')->comment('企业类型')->nullable();
            $table->json('district')->comment('所属地区')->nullable();
            $table->json('industry')->comment('所属行业')->nullable();
            $table->string('address')->comment('公司地址')->nullable();
            $table->integer('zip_code')->comment('邮编')->nullable();
            $table->string('fax')->comment('传真')->nullable();
            $table->string('url')->comment('公司网址')->nullable();
            $table->integer('license_id')->comment('执照文件id')->nullable();
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
        Schema::dropIfExists('company');
    }
}
