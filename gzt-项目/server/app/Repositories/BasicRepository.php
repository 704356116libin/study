<?php
namespace App\Repositories;
use App\Models\Basic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Token;

/**
 * Created by PhpStorm.
 * User: bin
 * Date: 2018/10/29
 * Time: 13:58
 */

class BasicRepository
{
    static private $basicRepository;
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {

    }
    /**
     * 单例模式
     */
    static public function getBasicRepository(){
        if(self::$basicRepository instanceof self)
        {
            return self::$basicRepository;
        }else{
            return self::$basicRepository = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
   /**
    * 获取基础表中指定id的信息
    */
   public function getBasicData(int $id){
       return Basic::find($id);
   }
}