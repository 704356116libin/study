<?php
/**
 * Created by PhpStorm.
 * User: bin
 * Date: 2019/4/30
 * Time: 9:37
 */

namespace App\Http\Controllers\Server;


trait DemoTrait
{
    public function demo(){
        return __CLASS__.'-'.__FUNCTION__;
    }
}