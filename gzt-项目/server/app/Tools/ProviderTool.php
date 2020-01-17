<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;

use App\Http\Resources\user\UserBaseResource;
use App\Http\Resources\user\UserSimpleResource;
use App\Interfaces\SmsInterface;
use App\Interfaces\UserInterface;
use App\Models\CompanyNotice;
use App\Models\User;
use App\Repositories\UserRepository;
use App\WebSocket\WebSocketClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Overtrue\EasySms\EasySms;
use swoole_client;
use swoole_websocket_server;

/**
 * 短信工具类
 */
class ProviderTool
{
    static private $providerTool;

    /**
     * 单例模式
     */
    static public function getProviderTool()
    {
        if (self::$providerTool instanceof self) {
            return self::$providerTool;
        } else {
            return self::$providerTool = new self;
        }
    }

    /**
     * 防止被克隆
     */
    private function _clone()
    {

    }
}