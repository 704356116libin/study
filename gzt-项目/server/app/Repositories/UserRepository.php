<?php
namespace App\Repositories;
use App\Models\User;

/**
 * Created by PhpStorm.
 * User: bin
 * Date: 2018/10/29
 * Time: 13:58
 */

class UserRepository
{
    static private $userRepository;
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {

    }
    /**
     * 单例模式
     */
    static public function getUserRepository(){
        if(self::$userRepository instanceof self)
        {
            return self::$userRepository;
        }else{
            return self::$userRepository = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 通过id  或者 id array获取用户
     */
    public function getUser($parms){
        if(is_int($parms)){
            return User::find($parms);
        }elseif (is_array($parms)){
            return User::whereIn('id',$parms)
                        ->get();
        }
    }
    /**
     * 检查电话号是否已经注册
     * @param $tel
     * @return bool
     */
    public function checkTelExsit($tel){
        $u=User::where('tel',$tel)
            ->get();
        if(count($u)>0){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 检查用户名是否已经注册
     */
    public function  checkNameExsit($name){
        $u=User::where('name',$name)
            ->get();
        if(count($u)>0){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 检查邮箱是否已经注册
     */
    public function  checkEmailExsit($email){
        $u=User::where('email',$email)
            ->where('email','!=',null)
            ->get();
        if(count($u)>0){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 检查email_token是否已经存在
     */
    public function  checkEmailTokenExsit($token){
        $u=User::where('email_token',$token)
            ->get();
        if(count($u)>0){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 通过邮箱拿到用户
     */
    public function getUserByEmail($email)
    {
       return User::where('email',$email)->first();
    }
    /**
     * 通过手机拿到用户
     */
    public function getUserByTel($tel)
    {
        return User::where('tel',$tel)->first();
    }
    /**
     * 更新用户的信息
     * @param $tel
     * @return mixed
     */
    public function updateUserData(User $user,array $data)
    {
           return $user->update($data);
    }
    /**
     * 检查api_token是否已经存在
     */
    public function  checkApiTokenExsit($token){
        $u=User::where('api_token',$token)
            ->get();
        if(count($u)>0){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 获取用户表当前最大id
     */
    public function  getUserMaxId(){
        return User::max('id');
    }
}