<?php
namespace App\Interfaces;

use Illuminate\Http\Request;

interface BusinessManagementInterface
{
    public function enterpriseInfoSave($data); //企业认证
    public function allRoles($data);//职务列表
    public function addRole($data);//添加职务
}