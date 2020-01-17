<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DingoController extends Controller
{
    /**
     * 返回版本
     * @return string
     */
    public function version(){
        return 'this version is v1';
    }
    public function version2(){
        return 'this version is v2';
    }
    public function test(){
        return 'teset';
    }
    /**
     *passport驱动api验证测试
     * @param Request $request
     * @return UserResource
     */
    public function api_test(Request $request){
        return auth('api')->id();
    }
}
