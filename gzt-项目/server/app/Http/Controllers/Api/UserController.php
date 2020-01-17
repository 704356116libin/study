<?php

namespace App\Http\Controllers\Api;


use App\Http\Resources\user\UserCardResource;
use App\Models\User;
use App\Tools\FunctionTool;
use App\Tools\RoleAndPerTool;
use App\Tools\UserTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 用户Api 主控制器
 * Class UserController
 * @package App\Http\Controllers\Api
 */
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
     * 获取某用户的个人名片信息
     * @param $name
     */
    public function getUserCardInfo(Request $request)
    {
        $user_id=$request->get('user_id');
        $userId=FunctionTool::decrypt_id($user_id);
        $user_id=$userId<0?$user_id:FunctionTool::decrypt_id($user_id);
        $user=auth('api')->user();
        $users=$user_id===null?$user:User::find($user_id);
        //当前公司id
        $users->present_company_id = $user->current_company_id;

        return [
            'status'=>'success',
            'data'=>new UserCardResource($users)
        ];
    }
    /**
     * 变更用户当前企业
     * @param $company
     */
    public function alterCurrentCompany(Request $request)
    {
        return $this->userTool->alterCurrentCompany($request->company_id);
    }
    /**
     *拉取用户的基本信息
     */
    public function getLoginUserInfo(Request $request){
        return $this->userTool->getLoginUserInfo();
    }
    /**
     * 获取个人名片
     */
    public function getPersonalCard()
    {
        return $this->userTool->getPersonalCard();
    }
    /**
     * 更新用户资料
     */
    public function eidtPersonalData(Request $request)
    {
        return $this->userTool->eidtPersonalData($request->all());
    }
    /**
     * 获取个人头像
     */
    public function getPersonalAvatar()
    {
        return $this->userTool->getPersonalAvatar();
    }
    /**
     * 更新头像
     */
    public function editPersonalAvatar(Request $request)
    {
        return $this->userTool->editPersonalAvatar($request);
    }
    /**
     * 邀请列表
     */
    public function invitelist()
    {
        return $this->userTool->invitelist();
    }

    /**
     * @param Request $request
     * @return array
     * 个人权限数组
     */
    public function permissions(Request $request)
    {
        $user=auth('api')->user();
        $company_id=$request->get('company_id');
        $company_id=$company_id===null?$user->current_company_id:FunctionTool::decrypt_id($company_id);
        return RoleAndPerTool::get_user_c_per($user->id,$company_id);
    }
}
