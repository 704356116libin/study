<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('deduction_amount', 10, 2)->nullable()->comment('抵扣')->after('total_amount');
            $table->decimal('discount_amount', 10, 2)->nullable()->comment('折扣')->after('total_amount');
            $table->decimal('original_amount', 10, 2)->nullable()->comment('原价')->after('total_amount');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
}
