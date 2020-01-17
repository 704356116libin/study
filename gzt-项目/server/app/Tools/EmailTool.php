<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;
use App\Interfaces\EmailInterface;
use App\Mail\EmailNotify;
use App\Mail\EmailReset;
use App\Mail\EmailUnlink;
use App\Mail\EmailVerified;
use App\Models\User;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/**
 * 邮件工具类
 */
class EmailTool implements EmailInterface
{
    static private $emailTool;
    private $userTool;

    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
        $this->userTool=UserTool::getUserTool();
    }
    /**
     * 单例模式
     */
    static public function getEmailTool(){
        if(self::$emailTool instanceof self)
        {
            return self::$emailTool;
        }else{
            return self::$emailTool = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 发送邮件总接口
     * @param $user:邮件发送得对象
     * @param $type:发送得邮件类型
     * @param $data:可能发送得数据
     */
    public function sendEmail($user,$type,$data='')
    {
        // TODO: Implement sendEmail() method.
       switch ($type){
           case 'verify':
               Mail::to($user)->send(new EmailVerified($user));
               break;
           case 'unlink':
               Mail::to($user)->send(new EmailUnlink($user));
               break;
           case 'reset':
               Mail::to($user)->send(new EmailReset($user));
               break;
           case 'notify':
               Mail::to($user)->send(new EmailNotify($data));
           default :
               break;
       }
        return json_encode(['status'=>'success','message'=>'邮件发送成功']);
    }
    /**
     * 邮箱验证接口
     */
    public function userEmailVerify($token)
    {
        // TODO: Implement verifyEmail() method.
      return $this->userTool->userEmailVerify($token);
    }
    /**
     * 用户邮箱解绑接口
     */
    public function userEmailUnlink($token)
    {
        // TODO: Implement verifyEmail() method.
        return $this->userTool->userEmailUnlink($token);
    }
}