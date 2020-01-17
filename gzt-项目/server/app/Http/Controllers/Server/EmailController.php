<?php

namespace App\Http\Controllers\Server;

use App\Repositories\UserRepository;
use App\Tools\EmailTool;
use App\Tools\UserTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmailController extends Controller
{
    private $emailTool;
    private $userTool;
    private $userRepository;
    public function __construct()
    {
        $this->emailTool=EmailTool::getEmailTool();
        $this->userTool=UserTool::getUserTool();
        $this->userRepository=UserRepository::getUserRepository();
    }
    /**
     * 邮箱验证
     * @param $token:令牌
     * @return string:返回数据
     */
    public function userEmailVerify(Request $request,$token){
        return $this->emailTool->userEmailVerify($request,$token);
    }
    /**
     * 邮箱解绑
     * @param $token:令牌
     * @return string:返回数据
     */
    public function userEmailUnlink($token){
        return $this->emailTool->userEmailUnlink($token);
    }
    /**
     * 发送邮件
     */
    public function sendEmail(Request $request){
        if(!$this->userRepository->checkEmailExsit($request->email)){
            return json_encode(['status'=>'fail','message'=>'所输入的邮箱不是本站用户']);
        }
        $user=$this->userTool->getUserByEmail($request->email);
        return $this->emailTool->sendEmail($user,$request->type);
    }
    /**
     * 邮箱视图展示
     */
    public function showBlad(Request $request){
        $user = \App\Models\User::find(1);
        return new \App\Mail\EmailVerified($user);
    }
}
