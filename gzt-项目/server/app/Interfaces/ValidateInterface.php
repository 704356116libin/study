<?php
/**
 * Created by PhpStorm.
 * User: bin
 * Date: 2018/11/14
 * Time: 14:01
 */

namespace App\Interfaces;

/**
 * 所有数据验证的总接口
 * Interface ValidateInterface
 * @package App\Interfaces
 */
interface ValidateInterface
{
    public function captcha_validate(array $data);//图片验证码数据校验
    public function telcode_validate(array $data);//短信验证码数据校验
    public function register_validate(array $data);//注册表单验证
}