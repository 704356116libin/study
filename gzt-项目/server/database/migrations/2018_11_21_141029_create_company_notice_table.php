<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyNoticeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_notice', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id')->comment('公司/组织id');
            $table->unsignedInteger('c_notice_column_id')->comment('企业栏目id');
            $table->string('title',100)->comment('公告标题');
            $table->mediumText('content')->comment('公告内容');
            $table->string('type',20)->comment('公告类型');
            $table->string('organiser',20)->comment('发起人name');
            $table->unsignedInteger('order')->comment('排序字段')->default(0);
            $table->smallInteger('is_show')->comment('发布状态')->default(0);
            $table->smallInteger('is_draft')->comment('是否是草稿')->default(0);
            $table->smallInteger('is_top')->comment('是否置顶')->default(0);
            $table->smallInteger('browse_count')->comment('浏览次数')->default(0);
            $table->smallInteger('notified')->comment('是否进行通知过')->default(0);
            $table->smallInteger('allow_download')->comment('是否允许下载')->default(0);
            $table->mediumText('allow_user')->comment('可见人数组');
            $table->mediumText('guard_json')->comment('选择的部门/人员信息数据');
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
        Schema::dropIfExists('company_notice');
    }
}
