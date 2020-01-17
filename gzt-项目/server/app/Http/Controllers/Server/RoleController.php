<?php

namespace App\Http\Controllers\Server;
use App\Tools\RoleTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    private $PermissionTool;//权限工具类
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->RoleTool=RoleTool::getRoleTool();
    }
    /**
     * 拥有权限的用户创建新的角色
     * @param Request $request
     */
    public function addRole(Request $request){
        return $this->RoleTool->addRole($request);
    }
    /**
     * 给角色赋予权限
     * @param Request $request
     */
    public function editRolePermission(Request $request){
        return $this->RoleTool->editRolePermission($request);
    }
    /**
     * 为用户添加角色(主职务或兼职)
     * @param $user_id 用户id
     * @param $role 角色名
     * @return mixed
     */
    public function giveUserRole(Request $request){
        return $this->RoleTool->giveUserRole($request);
    }

    /**
     * 职务列表
     */
    public function roleList(){
        return $this->RoleTool->roleList();
    }
    /**
     * 删除角色
     */
    public function deleteRole(Request $request){
        return $this->RoleTool->deleteRole($request);
    }
}
