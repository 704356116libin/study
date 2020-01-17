<?php
/**
 * Created by PhpStorm.
 * User: bin
 * Date: 2018/10/29
 * Time: 14:03
 */

namespace App\Interfaces;


use Illuminate\Http\Request;

interface EmailInterface
{
    public function sendEmail($user,$type,$data);//发送邮件总接口
    public function userEmailVerify($token);//邮箱验证接口
    public function userEmailUnlink($token);//邮箱解绑接口
}