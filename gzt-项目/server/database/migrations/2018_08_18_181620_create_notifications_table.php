
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('通知用户id');
            $table->integer('company_id')->comment('所属公司id')->default(0);
            $table->integer('model_id')->comment('多态模型id')->default(0);
            $table->string('model_type')->comment('通知模型类名')->default('');
            $table->string('type',20)->comment('通知类型')->notnull();
            $table->mediumText('message')->comment('通知内容');
            $table->smallInteger('readed')->comment('是否已读')->default(0);
            $table->smallInteger('ws_pushed')->comment('是否进行过ws实时通知')->default(0);
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
        Schema::dropIfExists('notifications');
    }
}
