<?php
namespace App\Interfaces;
use Illuminate\Http\Request;

/**
 * 短信接口所要实现的功能
 */
interface SmsInterface
{
    public function getTemplete($code);//根据类型代码,返回短信模板名称
    public function getTelCode(Request $request);//获取短信验证码的接口
    public function sendSms($tel, $data, $template);//发送短信总接口
}