<?php
namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Token;

/**
 * Created by PhpStorm.
 * User: bin
 * Date: 2018/10/29
 * Time: 13:58
 */

class TokenRepository
{
    static private $tokenRepository;
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {

    }
    /**
     * 单例模式
     */
    static public function getTokenRepository(){
        if(self::$tokenRepository instanceof self)
        {
            return self::$tokenRepository;
        }else{
            return self::$tokenRepository = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 删除旧的access_token
     */
    public function deleteOldToken($token_ids,$user_id){
        return Token::whereIn('id',$token_ids)
                    ->where('user_id',$user_id)
                    ->delete();
    }
    /**
     * 得到用户指定设备上除最新的token记录外的tokenids
     */
    public function getTokenIdExceptNew($token_id,$user_id,$client_id){
        return Token::select(['id'])
                     ->where('id','!=',$token_id)
                     ->where('user_id',$user_id)
                     ->where('client_id',$client_id)
                     ->pluck('id');
    }
    /**
     * 得到用户所有设备上的tokenids
     */
    public function getTokenIds($user_id){
        return Token::select(['id'])
            ->where('user_id',$user_id)
            ->pluck('id');
    }
    /**
     * 删除oauth_refresh_tokens表中的废弃数据
     */
    public function deleteOldRefreshToken($token_ids){
        DB::table('oauth_refresh_tokens')
            ->whereIn('access_token_id',$token_ids)
            ->delete();
    }
    /**
     * 废除超时的token(包括access_token,以及refresh_token)
     */
    public function revokeToken(){
        DB::beginTransaction();
        try{
            Token::where('expires_at','<',date('Y-m-d H:i:s',time()))
                ->update(['revoked'=>1]);
            DB::table('oauth_refresh_tokens')
                ->where('expires_at','<',date('Y-m-d H:i:s',time()))
                ->update(['revoked'=>1]);
            DB::commit();
            Log::info('AccessToken回收成功');
        }catch (\Exception $e){
            Log::info($e->getMessage().'-'.'Token超时回收定时任务异常'.'--'.$e->getLine().'-'.$e->getFile().'-'.$e->getTrace());
        }
    }
}