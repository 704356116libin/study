<?php
/**
 * Created by PhpStorm.
 * User: bin
 * Date: 2018/11/14
 * Time: 14:01
 */

namespace App\Interfaces;
use App\Models\Company;
use App\Models\Department;
use Illuminate\Http\Request;

/**
 * 企业公告接口
 * Interface ValidateInterface
 * @package App\Interfaces
 */
interface DepartmentInterface
{
   public function addRootNode(array $data);//添加一个根节点
   public function appendNode(array $data);//在某个节点后追加子节点
   public function getAllTree(Request $request);//拿到某公司的完整部门树
   public function getNodeDescendantsTree($node_id);//拿到某节点的下级树
   public function addUserToDepartment(array $user_ids,int $department_id);//给某个部门添加人员
   public function removeDepartmentUser($user_ids,$department_id);//移除某个部门下的某个人
   public function addStaff($department_id,array $user_ids);//后台管理系统添加员工
   public function searchTree(Request $request);//树内搜索(暂时用不上)
   public function initCompanyTree(Company $company);//初始化公司的组织结构树
   public function batchEditDepartments($department_id,array $user_ids);//批量修改员工部门
   public function batchEditRoles($role_id,array $user_ids);//批量修改员工职务
   public function batchDisable(array $user_ids);//批量停用员工
   public function batchFreeze(array $user_ids);//批量冻结员工
   public function searchTel($tel);//手机号搜索查询
}