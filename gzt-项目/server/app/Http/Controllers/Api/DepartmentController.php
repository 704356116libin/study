<?php

namespace App\Http\Controllers\Api;

use App\Tools\DepartmentTool;
use App\Tools\FunctionTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 企业部门控制器
 * Class DepartmentController
 * @package App\Http\Controllers\Api
 */
class DepartmentController extends Controller
{
    private $departmentTool;
    /**
     * DepartmentController constructor.
     */
    public function __construct()
    {
        $this->departmentTool=DepartmentTool::getDepartmentTool();
    }
    /**
     * 某个节点后追加节点
     * @param Department $parentNode
     * @param array $data
     * @return bool
     */
    public function appendNode(Request $request)
    {
        return $this->departmentTool->appendNode($request->all());
    }
    /**
     * 拿到某个公司完整的部门树
     * @param $company_id
     * @return mixed
     */
    public function getAllTree(Request $request)
    {
        return $this->departmentTool->getAllTree($request);
    }
    /**
     * 获取公司部门树,合作伙伴,外部联系人树
     */
    public function getCompanyAll(Request $request)
    {
        return $this->departmentTool->getCompanyAll($request);
    }
    /**
     * 拿到某个节点的子树
     * @param $node_id
     * @return mixed
     */
    public function getNodeDescendantsTree(Request $request)
    {
        return $this->departmentTool->getNodeDescendantsTree($request->node_id);
    }

    /**
     * 编辑部门
     */
    public function editDepartment(Request $request)
    {
        return $this->departmentTool->editDepartment($request->all());
    }

    /**
     * 删除部门
     * @param Request $request
     * @return array
     */
    public function deleteDepartment(Request $request){
        return $this->departmentTool->deleteDepartment($request->all());
    }
    /**
     * 树内搜索
     * @param Request $request
     */
    public function searchTree(Request $request)
    {
        return $this->departmentTool->searchTree($request);
    }
    /**
     * 获取单个部门详细信息--(子部门,拥有的人员)
     * @param $department_id
     */
    public function departmentDetail(Request $request){
        return $this->departmentTool->departmentDetail($request->node_id,$request->page_size,$request->now_page,$request->is_enable,$request->company_id,$request->activation);
    }

    /**
     * 新增员工
     */
    public function saveUserDate(Request $request)
    {
        return $this->departmentTool->saveUserDate($request->all());
    }
    /**
     * 员工信息(抽屉展示)
     */
    public function userDetail(Request $request)
    {
        return $this->departmentTool->userDetail($request->all());
    }
    /**
     * 编辑员工信息
     */
    public function editUserDetail(Request $request)
    {
        return $this->departmentTool->editUserDetail($request->all());
    }
    /**
     * 手机号或邮箱添加员工
     */
    public function addStallByTel(Request $request)
    {
        return $this->departmentTool->addStallByTel($request->telOrEmails);
    }
    /**
     * 批量修改员工部门
     */
    public function batchEditDepartments(Request $request)
    {
        return $this->departmentTool->batchEditDepartments($request->department_id,$request->user_ids);
    }
    /**
     * 批量修改员工职务
     */
    public function batchEditRoles(Request $request)
    {
        return $this->departmentTool->batchEditRoles($request->role_id,$request->user_ids);
    }
    /**
     * 批量停用员工
     */
    public function batchDisable(Request $request)
    {
        return $this->departmentTool->batchDisable($request->all());
    }
    /**
     * 批量冻结员工
     */
    public function batchFreeze(Request $request)
    {
        return $this->departmentTool->batchFreeze($request->all());
    }
    /**
     * 解冻
     */
    public function thaw(Request $request)
    {
        return $this->departmentTool->thaw($request->all());
    }
    /**
     * @param $tel
     * 手机号搜索查询用户
     */
    public function searchTel(Request $request)
    {
        return $this->departmentTool->searchTel($request->tel);
    }


    public function test(Request $request)
    {
//        return FunctionTool::encrypt_id(284);
//        return $this->departmentTool->deleteDepartment(['node_id'=>298]);
        return $this->departmentTool->test($request);
    }
}
