<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;

use App\Interfaces\TokenInterface;
use App\Models\User;
use App\Repositories\TokenRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Passport\Events\AccessTokenCreated;

/**
 * 令牌工具类--登陆验证以及授权
 */
class TokenTool implements TokenInterface
{
    static private $tokenTool;//用户令牌工具类
    private $tokenRepository;//用户令牌仓库
    private $functionTool;//主方法工具类
    private $http;
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
        $this->http=GuzzleHttpTool::getGuzzleHttpTool()->client;
        $this->tokenRepository=TokenRepository::getTokenRepository();
        $this->functionTool=FunctionTool::getFunctionTool();
    }
    /**
     * 单例模式
     */
    static public function getTokenTool(){
        if(self::$tokenTool instanceof self)
        {
            return self::$tokenTool;
        }else{
            return self::$tokenTool = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 拿到用户access_token(通过手机/密码授权)
     * request中参数:手机号(tel),password(密码字段),platform(平台标识)
     */
    public function getAccessToken($data)
    {
        // TODO: Implement getApiToken() method.
        if(!array_key_exists('platform',$data)){
            return json_encode(['status'=>'fail','message'=>'缺少平台标识']);
        }
        $response=null;
        try{
            $response = $this->http->post(url('/oauth/token'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => $data['platform']=='web'? config('client.web_id'):config('client.app_id'),
                    'client_secret' => $data['platform']=='web'? config('client.web_secret'):config('client.app_secret'),
                    'username' => $data['tel'],
                    'password' => $data['password'],
                    'scope' => '',
                ]
            ]);
        }catch(\Exception $e){
            $code = $e->getCode();
            return response()->json(['message' => $code==401?'账号/密码错误':$e->getMessage()], $code); 
        };
        return json_decode((string) $response->getBody(), true);
    }
    /**
     * 刷新用户访问令牌
     * @param $data
     */
    public function refreshToken($data)
    {
        // TODO: Implement refreshToken() method.
        if(!array_key_exists('platform',$data)){
            return json_encode(['status'=>'fail','message'=>'缺少平台标识']);
        }
        if(!array_key_exists('refresh_token',$data)){
            return json_encode(['status'=>'fail','message'=>'缺少refresh_token']);
        }
        $response=null;
        try{
            $response = $this->http->post(config('app.url').'/oauth/token', [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $data['refresh_token'],
                    'client_id' => $data['platform']=='web'? config('client.web_id'):config('client.app_id'),
                    'client_secret' => $data['platform']=='web'? config('client.web_secret'):config('client.app_secret'),
                    'scope' => '',
                ]
            ]);
        }catch(\Exception $e){
            dd($e);
            return json_encode(['status'=>$e->getCode(),'message'=>$e->getCode()==401?'账号/密码错误':'服务器开小差了.']);
        };
        return json_decode((string) $response->getBody(), true);
    }
    /**
     *用户令牌回收处理(只保留最新的一个令牌)
     */
    public function revokeUserToken($event)
    {
        // TODO: Implement revokeToken() method.
//        Log::info('TokenTool调用成功'.$event->tokenId);
        $token_id=$event->tokenId;
        $user_id=$event->userId;
        $client_id=$event->clientId;
        $revoke_token_ids=$this->tokenRepository->getTokenIdExceptNew($token_id,$user_id,$client_id);
        DB::beginTransaction();
        try{
            $this->tokenRepository->deleteOldRefreshToken($revoke_token_ids);
            $this->tokenRepository->deleteOldToken($revoke_token_ids,$user_id);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            Log::info($e->getMessage(),$e->getTrace(),$e->getCode());
        }
    }
    /**
     * 定时废除用户的访问令牌(revoke=1)(供定时任务调用)
     * @param $event
     */
    public function revokeToken()
    {
        // TODO: Implement revokeToken() method.
            $this->tokenRepository->revokeToken();
    }
    /**
     * 废除用户的所有token
     * @param $user_id
     */
    public function revokeUserAllToken($user_id)
    {
        // TODO: Implement revokeUserAllToken() method.
        $revoke_token_ids=$this->functionTool->convertEloquentToArray($this->tokenRepository->getTokenIds($user_id),['id']);
        DB::beginTransaction();
        try{
            $this->tokenRepository->deleteOldRefreshToken($revoke_token_ids);
            $this->tokenRepository->deleteOldToken($revoke_token_ids,$user_id);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            Log::info($e->getMessage(),$e->getTrace(),$e->getCode());
        }
    }
}