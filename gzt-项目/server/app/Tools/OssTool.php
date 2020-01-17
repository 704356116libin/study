<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;

use App\Interfaces\OssInterface;
/**
 * 云存储工具类
 */
class OssTool implements OssInterface
{
    static private $ossTool;
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
    }
    /**
     * 单例模式
     */
    static public function getOssTool(){
        if(self::$ossTool instanceof self)
        {
            return self::$ossTool;
        }else{
            return self::$ossTool = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }

}