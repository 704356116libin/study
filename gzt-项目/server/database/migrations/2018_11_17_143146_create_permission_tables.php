<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        /**
         * 基础权限权限表
         */
        Schema::create($tableNames['permissions'], function (Blueprint $table) use($columnNames){
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('description');
            $table->string('guard_name');//这东西比较特殊
            $table->smallInteger('is_personal')->comment('标识是否为单个用户提供的增值服务')->default(0);//单用户增值服务标识
        });
        /**
         * 角色表
         */
        Schema::create($tableNames['roles'], function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('角色名称');
            $table->string('description')->comment('角色描述');
            $table->string('guard_name');//这东西比较特殊
            $table->smallInteger('is_personal')->comment('标识是否为单个用户提供的增值服务')->default(0);//单用户增值服务标识
        });
        /**
         * 模型权限直接对接表(防止以后用户与权限直接对接)(包含公司拥有什么权限,用户拥有什么权限)
         */
        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames) {
            $table->unsignedInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type', ]);

//            $table->foreign('permission_id')
//                ->references('id')
//                ->on($tableNames['permissions'])
//                ->onDelete('cascade');

            $table->primary(['permission_id', $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
        });
        /**
         * 模型拥有的角色
         */
        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames) {
            $table->unsignedInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type', ]);//创建联合索引

//            $table->foreign('role_id')
//                ->references('id')
//                ->on($tableNames['roles'])
//                ->onDelete('cascade');

            $table->primary(['role_id', $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
        });
        /**
         * 角色职务拥有的权限
         */
        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames) {
            $table->unsignedInteger('permission_id');
            $table->unsignedInteger('role_id');

//            $table->foreign('permission_id')
//                ->references('id')
//                ->on($tableNames['permissions'])
//                ->onDelete('cascade');

//            $table->foreign('role_id')
//                ->references('id')
//                ->on($tableNames['roles'])
//                ->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
            app('cache')->forget('spatie.permission.cache');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('permission.table_names');

        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);
    }
}
