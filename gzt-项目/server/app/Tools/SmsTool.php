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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Overtrue\EasySms\EasySms;
/**
 * 短信工具类
 */
class SmsTool implements SmsInterface
{
    static private $smsTool;
    private $easySms;//短信服务接口
    private $validateTool;//数据验证总接口
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
        $this->easySms= new EasySms(config('sms'));
        $this->validateTool=ValidateTool::getValidateTool();
    }
    /**
     * 单例模式
     */
    static public function getSmsTool(){
        if(self::$smsTool instanceof self)
        {
            return self::$smsTool;
        }else{
            return self::$smsTool = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 获取短信模板
     * @param $code:短信类型代码
     */
    public function getTemplete($code)
    {
        // TODO: Implement getTemplete() method.
        $template='';
        switch ($code){
            case 'register':
                $template='SMS_84725017';//注册模板
                break;
            case 'reset':
                $template= 'SMS_84725016';//找回密码模板
                break;
//            case 'verify':
//                break;
            default :
                $template = 'SMS_84725015';//信息变更模板
                ;
        }
        return $template;
    }
    /**
     * 发送短信总接口
     */
    public function sendSms($tel, $data, $template)
    {
        // TODO: Implement sendSms() method.
        return $this->easySms->send($tel, [
            'template' => $template,
            'data' => $data,
        ]);
    }
    /**
     * 发送短信验证码
     */
    public function getTelCode(Request $request)
    {
        // TODO: Implement getTelCode() method.
        $validator =  $this->validateTool->captcha_validate($request->all());//图片验证码校验
        if (is_array($validator)) {
            return json_encode($validator);
        }
        $tel=$request->tel;
        if(preg_match(config('regex')['tel'],$tel)==1) {
            $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);//生成验证码
            $template=$this->getTemplete($request->type);//模板代码
                try {
//                    $this->sendSms($tel,['code'=>$code],$template);
                    $key = $tel.'_'.str_random(10);
                    $expiredAt = now()->addMinutes(30);
                    cache([$key=>[ 'tel_code' => $code,'tel'=>$tel]],$expiredAt);//验证码存入缓存方便验证
                    return json_encode(['status'=>'success','message'=>'验证码发送成功','tel_code'=>$code,'tel_key'=>$key]);
                } catch (\Exception $e) {
                    return json_encode(['status'=>'fail','message'=>'验证码发送失败,稍后再试']);
                }
        }else{
            return json_encode(['status'=>'fail','message'=>'请输入正确的手机号']);//不够60秒稍后重试
        }
    }
}