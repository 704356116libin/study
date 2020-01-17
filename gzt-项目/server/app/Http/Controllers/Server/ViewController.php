<?php

namespace App\Http\Controllers\Server;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 所有返回视图的控制器
 * Class ViewController
 * @package App\Http\Controllers\Server
 */
class ViewController extends Controller
{
    /**
     * 返回注册页面
     */
   public function register(){
       return view('register');
   }
    /**
     * 返回找回密码页面
     */
   public function reset(){
       return view('auth.reset');
   }
    public function ajax(){
        return view('ajax');
    }
    /**
     * 返回邮箱重置密码页面
     */
    public function emailResetPwd($token){
        $user = User::where('email_token',$token)->first();
        $state=true;
        if (is_null($user)) {
         $state=false;
        }
        return view('auth.email_reset', compact(['token','state']));
    }
}
