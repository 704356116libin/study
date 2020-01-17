<?php

namespace App\Listeners;

use App\Tools\TokenTool;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Events\AccessTokenCreated;
use Laravel\Passport\Token;

/**
 * 用户令牌回收处理
 */
class RevokeOldTokens
{
    private $tokenTool;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
       $this->tokenTool=TokenTool::getTokenTool();
    }

    /**
     * Handle the event.
     *
     * @param  AccessTokenCreated  $event
     * @return void
     */
    public function handle(AccessTokenCreated $event)
    {
        Log::info('access_token_id:'.$event->tokenId.'-client_id:'.$event->clientId.'-user_id:'.$event->userId);

//          Token::where('id', '!=', $event->tokenId)
//            ->where('user_id', $event->userId)
//            ->where('client_id', $event->clientId)
//            ->where('expires_at', '<',date('Y-m-d H:i:s',time()))
//            ->orWhere('revoked', true)
//            ->delete();
        $this->tokenTool->revokeUserToken($event);
    }
}
