<?php

namespace App\Interfaces;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * 角色接口所要实现的功能
 */
interface RoleAndPerInterface
{
    public static function copyBasicToCompany(Company $company,User $user);//给企业添加基础的角色信息
    public static function user_has_c_per(int $user_id,int $company_id,array $pers,string $type):bool ;//判断某用户在某个企业是否拥有某个权限
    public static function get_user_c_per(int $user_id,int $company_id):array ;//抽取某用户在某企业拥有的所有权限名为数组
}