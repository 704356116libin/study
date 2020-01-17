<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/22
 * Time: 16:54
 */

namespace App\Tools;



use Illuminate\Support\Facades\Auth;

/**
 * 邀请/推广工具类
 * Class SpreadTool
 * @package App\Tools
 */
class SpreadTool
{
    static private  $spreadTool;
    /**
     * PerTimeOutTool constructor.
     */
    private function __construct()
    {

    }

    /**
     * 单例模式
     */
    static public function getPerTimeOutTool(){
        if(self::$spreadTool instanceof SpreadTool)
        {
            return self::$spreadTool;
        }else{
            return self::$spreadTool=new SpreadTool();
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }


}