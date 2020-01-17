<?php

namespace App\Http\Controllers\Server;

use App\Tools\SocialiteTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SocialiteController extends Controller
{

    /**
     * SmsController constructor.
     * @param $smsTool
     */
    private $tool;
    public function __construct(SocialiteTool $tool)
    {
        $this->tool = $tool;
    }

    public function getWxOpenIdAuthUrl(Request $request)
    {
        $url = $this->tool->getWxOpenCode($request->url);

        return $data=[
                    'code' => 1,
                    'url' => $url,
            ];
    }

}
