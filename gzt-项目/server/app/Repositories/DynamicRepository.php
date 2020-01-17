<?php

namespace App\Repositories;

use App\Models\Basic;
use App\Models\Dynamic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Token;

/**
 * 动态模块仓库类
 * Created by PhpStorm.
 * User: bin
 * Date: 2018/10/29
 * Time: 13:58
 */
class DynamicRepository
{
    static private $dynamicRepository;

    /**
     *私有构造函数防止new
     */
    private function __construct()
    {

    }
    /**
     * 单例模式
     */
    static public function getInstance()
    {
        if (self::$dynamicRepository instanceof self) {
            return self::$dynamicRepository;
        } else {
            return self::$dynamicRepository = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone()
    {

    }
    /**
     * 新增一条动态数据
     */
    public function add(array $data)
    {
        return Dynamic::create($data);
    }
    /**
     *更新动态数据
     */
    public function update($id,array $data)
    {
        return Dynamic::where('id',$id)
                ->update($data);
    }
}