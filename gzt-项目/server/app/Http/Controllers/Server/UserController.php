<?php

namespace App\Http\Controllers\Server;


use App\Tools\TokenTool;
use App\Tools\UserOssTool;
use App\Tools\UserTool;
use App\Tools\ValidateTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    private $userTool;//用户工具类
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->userTool=UserTool::getUserTool();
    }
    /**
     * 用户注册的逻辑
     */
    public function register(Request $request)
    {
        return $this->userTool->register($request);
    }
    /**
     * 检查用户手机号是否存在
     */
    public function checkTelExsit(Request $request){
        return $this->userTool->checkTelExsit($request->tel);
    }
    /**
     * 检查用户邮箱是否存在
     */
    public function checkEmailExsit(Request $request){
        return $this->userTool->checkEmailExsit($request->email);
    }
    /**
     * 通过用户邮箱密码重置
     * @param Request $request
     */
    public function userSetPwdByEmail(Request $request){
        return $this->userTool->userSetPwdByEmail($request);
    }
    /**
     * 用户手机验证
     * @param Request $request
     */
    public function userTelVerify(Request $request){
        return $this->userTool->userTelVerify($request);
    }
    /**
     * 用户手机解绑
     * @param Request $request
     */
    public function userTelUnlink(Request $request){
        return $this->userTool->userTelUnlink($request);
    }
    /**
     * 通过用户手机密码重置
     * @param Request $request
     */
    public function userSetPwdByTel(Request $request){
        return $this->userTool->userSetPwdByTel($request);
    }
    /**
     * 为用户添加角色
     * @param $user_id 用户id
     * @param $role 角色名
     * @return mixed
     */
    public function giveUserRole(Request $request){
       return $this->userTool->giveUserRole($request);
    }
}
