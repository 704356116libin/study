<?php

namespace App\Http\Controllers\Server;

use App\Tools\SmsTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SmsController extends Controller
{
    private $smsTool;//短信工具类

    /**
     * SmsController constructor.
     * @param $smsTool
     */
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
