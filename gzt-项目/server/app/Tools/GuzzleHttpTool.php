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
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/**
 * GuzzleHttp网络请求库
 */
class GuzzleHttpTool
{
    static private $guzzleHttpTool;
    public $client;//请求实例
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
        $this->client=new Client();
    }
    /**
     * 单例模式
     */
    static public function getGuzzleHttpTool(){
        if(self::$guzzleHttpTool instanceof self)
        {
            return self::$guzzleHttpTool;
        }else{
            return self::$guzzleHttpTool = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
}