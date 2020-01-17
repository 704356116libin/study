<?php

namespace App\Http\Controllers\Server;

use App\Models\User;
use App\Tools\TokenTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 授权控制器
 * Class TokenController
 * @package App\Http\Controllers\Server
 */
class TokenController extends Controller
{
    private $tokenTool;
    public function __construct()
    {
        $this->tokenTool=TokenTool::getTokenTool();
    }
    /**
     * 获取用户access_token
     */
    public function getAccessToken(Request $request){
        return $this->tokenTool->getAccessToken($request->all());
    }
    /**
     * 刷新用户access_token(暂时没用)
     */
    public function refreshToken(Request $request){
        return $this->tokenTool->refreshToken($request->all());
    }
}
