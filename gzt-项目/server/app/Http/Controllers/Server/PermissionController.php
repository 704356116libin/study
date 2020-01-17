<?php

namespace App\Http\Controllers\Server;
use App\Tools\PermissionTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    private $PermissionTool;//权限工具类
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->PermissionTool=PermissionTool::getPermissionTool();
    }

    /**
     * 判断是否有权限
     */
    public function canPermission(Request $request){
        return $this->PermissionTool->canPermission($request);
    }
    /**
     * 创建新的权限类型
     */
    public function addPermission(Request $request){
        return $this->PermissionTool->addPermission($request);
    }
}
