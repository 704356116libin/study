<?php
/**
 * Created by PhpStorm.
 * User: bin
 * Date: 2018/10/29
 * Time: 14:03
 */

namespace App\Interfaces;


use App\Models\User;
use Illuminate\Http\Request;

interface UserInterface
{
    public function updateUserData(User $user,array $data);//更新指定用户的用户信息
    public function checkTelExsit($tel);//验证电话是否存在
    public function checkNameExsit($name);//验证用户名是否存在
    public function checkEmailExsit($email);//验证邮箱是否存在
    public function makeEmailToken();//生成为一个email_token值,进行邮箱验证
    public function userEmailVerify($token);//用户邮箱验证(EmailController)
    public function userEmailUnlink($token);//用户邮箱解绑(EmailController)
    public function userSetPwdByEmail(Request $request);//通过用户邮箱密码重置
    public function userTelVerify(Request $request);//用户手机验证
    public function userTelUnlink(Request $request);//用户手机解绑
    public function userSetPwdByTel(Request $request);//通过用户手机密码重置
    public function getUserByEmail($email);//通过邮箱拿到用户
    public function getUserByTel($tel);//通过手机号拿到用户
    public function giveUserRole(Request $request);//为用户添加角色
    public function getRandomUser();//随机抽取一个已存在的用户
    public function register(Request $request);//用户注册
    public function alterCurrentCompany($company_id);//更改用户当前公司
    public function getLoginUserInfo();//获取当前登陆用户的基础信息
}