<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;

use ReflectionClass;

/**
 * 动态加载类模块
 */
class InstanceTool
{
    /**
     * 动态映射类
     * @return object
     * @throws \ReflectionException
     */
    public static function newInstance()
    {
        $arguments = func_get_args();
        $className = array_shift($arguments);//取出类名
        $class = new ReflectionClass($className);
        $args=array_slice($arguments,0);
        return $class->newInstanceArgs($args);
    }
}