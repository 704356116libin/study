<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseControllser;

/**
 * 所有提供Api接口的控制类都继承该类
 */
class Controller extends BaseControllser
{
    use Helpers;
}
