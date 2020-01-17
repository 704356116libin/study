<?php

namespace App\Http\Controllers\Api;

use App\Tools\SmsTool;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    private $smsTool;//短信工具类

    public function __construct()
    {
        $this->smsTool = SmsTool::getSmsTool();
    }
    /**
     * 注册,重置,解绑时获取短信验证码
     * @param Request $request
     */
    public function getTelCode(Request $request)
    {
        return $this->smsTool->getTelCode($request);
    }
}
