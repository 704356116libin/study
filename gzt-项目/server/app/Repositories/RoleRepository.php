<?php
namespace App\Repositories;
use App\Models\Role;
use Illuminate\Support\Facades\DB;


/**
 * Created by PhpStorm.
 * User: bin
 * Date: 2018/10/29
 * Time: 13:58
 */

class RoleRepository
{
    /**
     * 获取某用户在某个企业下的角色id数组
     */
    public static function getUserCompanyRoleIds($user_id,$company_id){
        return DB::table('company_user_role')->where('user_id',$user_id)
            ->where('company_id',$company_id)
            ->pluck('role_id')
            ->toArray();
    }
    /**
     * 获取指定id的角色
     */
    public static function getRolesByIds(array $ids){
        return Role::whereIn('id',$ids)->get();
    }
}