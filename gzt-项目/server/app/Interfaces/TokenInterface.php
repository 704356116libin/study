<?php
/**
 * Created by PhpStorm.
 * User: bin
 * Date: 2018/10/29
 * Time: 14:03
 */

namespace App\Interfaces;


use Illuminate\Http\Request;

interface TokenInterface
{
    public function getAccessToken($data);//拿到用户访问令牌
    public function refreshToken($data);//刷新用户访问令牌
    public function revokeUserToken($event);//废除某用户的访问令牌(令牌自动回收,只保留最新的一个)
    public function revokeUserAllToken($user_id);//废除某用户的所有访问令牌
    public function revokeToken();//废除全部用户的访问令牌,revoke字段标识为1
}