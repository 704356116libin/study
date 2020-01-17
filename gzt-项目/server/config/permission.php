<?php

return [

    'models' => [

        'permission' => \App\Models\Permission::class,

        'role' => \App\Models\Role::class,

    ],

    'table_names' => [
        'roles' => 'roles',//网站总角色表
        'permissions' => 'permissions',//基础权限表
        'model_has_permissions' => 'model_has_permissions',//公司有什么权限表
        'model_has_roles' => 'model_has_role',//公司拥有的职务
        'role_has_permissions' => 'role_per',//角色拥有的权限表
    ],

    'column_names' => [

        /*
         * Change this if you want to name the related model primary key other than
         * `model_id`.
         *
         * For example, this would be nice if your primary keys are all UUIDs. In
         * that case, name this `model_uuid`.
         */
        'model_morph_key' => 'model_id',
    ],

    /*
     * By default all permissions will be cached for 24 hours unless a permission or
     * role is updated. Then the cache will be flushed immediately.
     */

    'cache_expiration_time' => 60 * 24,

    /*
     * When set to true, the required permission/role names are added to the exception
     * message. This could be considered an information leak in some contexts, so
     * the default setting is false here for optimum safety.
     */

    'display_permission_in_exception' => false,
];
