<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;

use App\Interfaces\Sms;
use App\Interfaces\SmsInterface;
use App\Interfaces\ValidateInterface;
use App\Repositories\BasicRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Overtrue\EasySms\EasySms;
/**
 * 短信工具类
 */
class ValidateTool implements ValidateInterface
{
    static private $validateTool;
    private $basicRepository;//基础数据仓库
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
        $this->basicRepository=BasicRepository::getBasicRepository();
    }
    /**
     * 单例模式
     */
    static public function getValidateTool(){
        if(self::$validateTool instanceof self)
        {
            return self::$validateTool;
        }else{
            return self::$validateTool = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 图片验证码的数据校验
     * @param array $data
     */
    public function captcha_validate(array $data)
    {
        // TODO: Implement captcha_validate() method.
        $validator = Validator::make($data, [
            'captcha_code' => 'required|string',
            'captcha_key' => 'required|string',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = $errors->messages();
            return [
                'status' => 'fail',
                'message'=>'数据验证未通过',
                'error'=>[
                    'captcha_code' => ($errors->has('captcha_code') ? implode(',', $messages['captcha_code']) : null),
                    'captcha_key' => ($errors->has('captcha_key') ? implode(',', $messages['captcha_key']) : null),
                ]
            ];
        } else {
            $captchaData = Cache::get($data['captcha_key']);
            if (!$captchaData) {
                return  [
                    'status' => 'fail',
                    'message' => '图片验证码已失效',
                ];
            }
            if (!hash_equals($captchaData['captcha_code'], $data['captcha_code'])) {
                Cache::forget($data['captcha_key']);   // 验证错误就清除缓存
                return  [
                    'status' => 'fail',
                    'message' => '验证码错误',
                ];
            }
            Cache::forget($data['captcha_key']);   // 验证通过也清除缓存
            return true;
        }
    }
    /**
     * 注册表单验证
     * @param array $data
     */
    public function register_validate(array $data)
    {
        // TODO: Implement register_validate() method.
        $validator = Validator::make($data, [
            'tel' => 'required|string|max:11|unique:users|regex:' . config('regex.tel'),
            'password' => 'required|string|min:6|confirmed|regex:' . config('regex.password'),
            'tel_code' => 'required|string',
            'tel_key' => 'required|string',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = $errors->messages();
            return [
                'status' => 'fail',
                'message'=>'数据验证未通过',
                'error'=>[
                    'tel' => ($errors->has('tel') ? implode(',', $messages['tel']) : null),
                    'password' => ($errors->has('password') ? implode(',', $messages['password']) : null),
                    'tel_code' => ($errors->has('tel_code') ? implode(',', $messages['tel_code']) : null),
                    'tel_key' => ($errors->has('tel_key') ? implode(',', $messages['tel_key']) : null),
                ]
            ];
        } else {
            $telData = Cache::get($data['tel_key']);
            if (!$telData) {
                return  [
                    'status' => 'fail',
                    'message' => '短信验证码已失效',
                ];
            }
            if (!hash_equals($telData['tel_code'], $data['tel_code'])) {
                Cache::forget($data['tel_key']);   // 验证错误就清除缓存
                return  [
                    'status' => 'fail',
                    'message' => '验证码错误',
                ];
            }
            Cache::forget($data['tel_key']);   // 验证通过也清除缓存
            return true;
        }
    }
    /**
     * 短信验证码的验证
     * @param array $data
     */
    public function telcode_validate(array $data)
    {
        // TODO: Implement telcode_validate() method.
        $validator = Validator::make($data, [
            'tel' => 'required|string|max:11|regex:'.config('regex.tel'),
            'tel_key' => 'required|string',
            'tel_code'=>'required|string',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = $errors->messages();
            return [
                'status' => 'fail',
                'message'=>'数据验证未通过',
                'error'=>[
                    'tel_key' => ($errors->has('tel_key') ? implode(',', $messages['tel_key']) : null),
                    'tel_code' => ($errors->has('tel_code') ? implode(',', $messages['tel_code']) : null),
                ]
            ];
        } else {
            $telData = Cache::get($data['tel_key']);
            if (!$telData) {
                return  [
                    'status' => 'fail',
                    'message' => '短信验证码已失效',
                ];
            }
            if (!hash_equals($telData['tel'], $data['tel'])) {
                return  [
                    'status' => 'fail',
                    'message' => '手机号与验证码不匹配',
                ];
            }
            if (!hash_equals($telData['tel_code'], $data['tel_code'])) {
                return  [
                    'status' => 'fail',
                    'message' => '验证码错误',
                ];
            }
            Cache::forget($data['tel_key']);   // 验证错误就清除缓存
            return true;
        }
    }
    /**
     * 敏感字符过滤
     * @param array $data
     * @return array
     */
    public function sensitive_word_validate(array $data){
        preg_match('/^[\s\S]*('. $this->basicRepository->getBasicData(config('basic.sensitive_word'))->body.')[\s\S]*$/',$data['name'],$matches);
        if(count($matches)==0){
            return true;
        }else{
            return [
                'status'=>'fail',
                'message'=>'存在敏感字符 ('.$matches[1].') 请修正.',
            ];
        }
    }
}