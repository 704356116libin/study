<?php

namespace App\Http\Controllers\Server;

use App\Mail\EmailVerified;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use Overtrue\EasySms\EasySms;

class CacheController
{
  private $EXT='.txt';
  private $dir;
    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->dir=public_path().'/caches';
    }

    /**
     * 缓存逻辑思想测试
     * @param Request $request
     * @return string
     */
    public function cache(Request $request){
        $name=$request->name;
        $value=$request->value;
        $type=$request->type;
        $filename=$this->dir.'/'.$name.$this->EXT;
        switch ($type){
            case 'alive':
                $aliveTime=60;//缓存生效时间50秒
                return $this->aliveCache($value,$filename,$type,$aliveTime);
                break;
            case 'save'://存储缓存
                if($value!==''){
                    if(!is_dir($this->dir)){
                        mkdir($this->dir,0777);
                    }
                    file_put_contents($filename,$value);
                }
                return json_encode([
                    'type'=>$type,
                    'status'=>'success',
                    'message'=>'缓存写入成功',
                ]);
                break;
            case 'get'://获取缓存
                $str='';
                if(file_exists($filename)){
                    $str=file_get_contents($filename);
                }
                return json_encode([
                    'type'=>$type,
                    'status'=>'success',
                    'message'=>$str,
                ]);
                break;
            default://清空缓存
                try{
                    if(file_exists($filename)){
                        unlink($filename);
                    }
                    return json_encode([
                        'type'=>$type,
                        'status'=>'success',
                        'message'=>'缓存清除成功',
                ]);
                }catch (\Exception $e){
                    return json_encode([
                        'type'=>$type,
                        'status'=>'fail',
                        'message'=>'删除缓存失败',
                     ]);
                }
                break;
        }
    }
    /**
     *原生数据库连接测试
     */
    public function connect(){
        dd(sprintf('%011d','89'));
        $connect=mysqli_connect('localhost','homestead','secret','gzt');
        dd(mysqli_fetch_assoc(mysqli_query($connect,'select * from users '))) ;
    }
    public function aliveCache($value,$filename,$type,$aliveTime){
        date_default_timezone_set('PRC');
        $cacheTime=file_exists($filename)
                    ? json_decode(file_get_contents($filename),true)['cachetime']
                    :time();//截取缓存过期时间
//        dd($cacheTime<time(),$cacheTime,date('Y-m-d h:i:s',$cacheTime),date('Y-m-d H:i:s',time()+$aliveTime));
        if($cacheTime+$aliveTime<time()) {
            $cacheTime=time();
            if(file_exists($filename)){
                unlink($filename);
            }
            if ($value !== '') {
                if (!is_dir($this->dir)) {
                    mkdir($this->dir, 0777);
                }
                file_put_contents($filename,json_encode(['cachetime'=>$cacheTime,'data'=>$value]));
            }
            return json_encode([
                'type' => $type,
                'status' => 'success',
                'message' => '缓存写入成功:'.date('Y-m-d H:i:s',$cacheTime),
            ]);
        }else{
            $str='';
            if(file_exists($filename)){
                $str=date('H:i:s',json_decode(file_get_contents($filename),true)['cachetime']).json_decode(file_get_contents($filename),true)['data'];
            }
            return json_encode([
                'type'=>$type,
                'status'=>'success',
                'message'=>$str,
            ]);
        }
    }

    /**
     * 短信接口测试
     * @return array
     */
    public function dddd(){
//        $config=config('sms');
//        $easySms = new EasySms($config);
//        try{
//            return $easySms->send(15237358570, [
//                'template' => 'SMS_84725019',
//                'data' => [
//                    'code' => '66',
//                ],
//            ]);
//        }catch (\Exception $e){
//            dd($e);
//        }

    }

    /**
     * 图片验证码测试
     */
    public function captcha(){
        captcha();
        return captcha();
    }

    /**
     * 数据验证测试
     * @param array $data
     * @return mixed
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
//            'name' => 'required|string|min:6|max:16|unique:users|regex:/^(?!(\d+)$)[\x{4e00}-\x{9fa5}a-zA-Z\d\-_]{6,16}$/u',
//            'email' => 'required|string|email|max:255|unique:users',
//            'password' => 'required|string|min:6|max:16|confirmed|regex:/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{6,16}$/',
//            'tel'=>'required|string|min:11|unique:users|telnum|regex:/^1[3456789][0-9]{9}$/',
//            'tel_code'=>'required|string|code',
            'captcha'=>'required|captcha',
        ]);
    }
    public function checkCaptcha(Request $request){
        try{
            $this->validator($request->all())->validate();
            return json_encode(
                [
                    'status'=>'success',
                    'message'=>'验证码正确',
                ]
            );
        }catch (\Exception $e){
            return json_encode(
                [
                    'status'=>'fail',
                    'message'=>'验证码不正确',
                ]
            );
        }

    }
    public function email(){


    }
}
