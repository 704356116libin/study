<?php
namespace App\Repositories;
use App\Models\User;
use App\Models\UserOss;

/**
 * Created by PhpStorm.
 * User: bin
 * Date: 2018/10/29
 * Time: 13:58
 */

class UserOssRepository
{
    static private $userOssRepository;
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {

    }
    /**
     * 单例模式
     */
    static public function getUserOssRepository(){
        if(self::$userOssRepository instanceof self)
        {
            return self::$userOssRepository;
        }else{
            return self::$userOssRepository = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    public function updateOss($id,array $data){
        return UserOss::find($id)
                ->update($data);
    }
}