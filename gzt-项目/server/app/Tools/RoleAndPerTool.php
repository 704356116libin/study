<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;

use App\Interfaces\RoleAndPerInterface;
use App\Models\Company;
use App\Models\User;
use App\Models\Role;
use App\Repositories\BasicRepository;
use App\Repositories\RoleRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 角色&权限功能类---全局通用(方法全部为静态)
 */
class RoleAndPerTool implements RoleAndPerInterface
{
    /**
     * 给企业添加基础的角色信息
     * @param Company $company
     */
    public static function copyBasicToCompany(Company $company,User $user)
    {
        $roles=BasicRepository::getBasicRepository()->getBasicData(config('basic.c_roles'))->load('roles')->roles;
        foreach ($roles as $role){
            $data=$role->getAttributes();
            unset($data['id']);
            preg_match('/(.*)-(.*)/', $data['name'] , $matches );
            $data['name']=$matches[1];
            $rol=Role::create($data);//复制角色
            //超级管理员赋予创建者
            $rol->syncPermissions($role->permissions);//同步角色相关权限
            if ($rol->hasPermissionTo('c_super_manage_per')){
                DB::table('company_user_role')->insert(['company_id'=>$company->id,'user_id'=>$user->id,'role_id'=>$rol->id]);
            }
            $company->assignRole($rol);//赋予公司角色
        }
    }
    /**
     * 判断某用户在某个企业是否拥有某个权限
     * @param int $user_id:目标用户
     * @param int $company_id:目标企业
     * @param array $pers:权限数组(具体是name/id待定)
     * @param string $type:判断模式.--any代表有任意一个权限就行--all代表必须拥有所有的权限
     * @return bool:true代表拥有指定的权限反之亦然
     */
    public static function user_has_c_per(int $user_id,int $company_id,array $pers,string $type):bool
    {
        if($company_id==0){
            return false;
        }
        $state=false;
        //获得某用户在某公司下拥有的角色id数组
        $role_ids=RoleRepository::getUserCompanyRoleIds($user_id,$company_id);
        //获得指定id的角色数组
        $roles= RoleRepository::getRolesByIds($role_ids);
        foreach ($roles as $key=>$role){
            try {
                if ($type == 'all') {
                    $state = $role->hasAllPermissions($pers);
                } else {
                    $state = $role->hasAllPermissions($pers);
                }
            } catch (\Exception $e) {
                Log::info($company_id.'--'.$pers[$key].'---读取权限错误/或者不存在相应的权限');
                return false;
            }
            if ($state){
                return $state;
            }
        }
        return $state;
    }
    /**
     * 抽取某用户在某个企业所拥有的权限为数组
     * @param int $user_id:目标用户
     * @param int $company_id:目标企业
     */
    public static function get_user_c_per(int $user_id,int $company_id):array
    {
        $data=[];
        //获得某用户在某公司下拥有的角色id数组
        $role_ids=RoleRepository::getUserCompanyRoleIds($user_id,$company_id);
        //获得指定id的角色数组
        $roles= RoleRepository::getRolesByIds($role_ids);
        foreach ($roles as $key=>$role){
            $data=array_merge($role->getAllPermissions()->pluck('name')->toArray(),$data);
        }
        return array_values(array_unique($data));
    }
    /**
     * 获取某企业下拥有指定权限的人员id数组
     */
    public static function get_company_target_per_users(int $company_id,array $pers):array{
        //先取出某企业所拥有的所有的角色
        $roles=Company::find($company_id)->roles;
        //循环判断角色是否有某些指定的权限
        $role_ids=[];//存放有指定权限的role_id
        foreach ($roles as $role){
            if($role->hasAllPermissions($pers)){
                $role_ids[]=$role->id;
            }
        }
        //查询有指定角色的人员
        $user_ids=DB::table('company_user_role')
            ->whereIn('role_id',$role_ids)
            ->get()
            ->pluck('user_id')
            ->toArray()
            ;
        return $user_ids;
    }
}