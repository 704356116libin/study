<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;
use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Overtrue\Socialite\SocialiteManager;

/**
 * 三方登陆工具类
 */
class SocialiteTool
{
    /**
     * @return \Overtrue\Socialite\User
     */
    public function wechat_login()
    {
        $config = [
            'wechat' => [
                'client_id' => 'wx68ce9558bc0a1381',
                'client_secret' => '7fa824503e544d9f2a3287d56903513f',
                'redirect' => '/',
            ],
        ];
        $socialite = new SocialiteManager($config);
        return $user = $socialite->driver('wechat')->user();
    }

    /**
     * @param $req_url
     * 请求的返回跳转链接
     * @return string
     */
    public function getWxOpenCode($req_url)
    {
            $url='https://open.weixin.qq.com/connect/oauth2/authorize';
            $params = [
                'appid' => config('pay.wechat.app_id'),
                'redirect_uri' => 'https://pst.pingshentong.com/'.$req_url,//被坑了一天,不要加urlEncode
                'response_type' => 'code',
                'scope' => 'snsapi_base',
                'state' => 'STATE',
            ];//请求拿到access_token的url参数数组
            $url .= '?' . http_build_query($params).'#wechat_redirect';//url拼接
            return $url;
//            return $this->http_curl($url);

    }

    /**
     * @param $code
     * @return mixed
     */
    public function getWeChatAccessToken($code){
        $url='https://api.weixin.qq.com/sns/oauth2/access_token';
        $params = [
            'appid' => config('pay.wechat.app_id'),
            'secret' => '7fa824503e544d9f2a3287d56903513f',
            'code' => $code,
            'grant_type'=>'authorization_code'
        ];//请求拿到access_token的url参数数组
        $url .= '?' . http_build_query($params);//url拼接
        return $this->http_curl($url);
    }

    /**
     * @param $url
     * @return mixed
     */
    public function http_curl($url){
        //用curl传参
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //关闭ssl验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch,CURLOPT_HEADER, FALSE);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);

        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }

}