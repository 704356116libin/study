<?php

namespace App\Http\Controllers\Api;

use App\Tools\UserOssTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 个人云存储控制器
 * Class OssController
 * @package App\Http\Controllers\Api
 */
class UserOssController extends Controller
{
    private $userOssTool;
    /**
     * UserOssController constructor.
     */
    public function __construct()
    {
        $this->userOssTool=UserOssTool::getUserOssTool();
    }
    /**
     * 创建一个目录
     * @param $path
     */
    public function makeDir(Request $request)
    {
        $user=auth('api')->user();
        return  $this->userOssTool->makeDir($request->path,$user);
    }
    /**
     * 删除一个目录
     * @param $path
     */
    public function deleteDir(Request $request)
    {
        $user=auth('api')->user();
        return  $this->userOssTool->deleteDr($request->path,$user);
    }
    /**
     * 拿到当前存储总容量
     * @param $rootPath
     */
    public function getNowSize(Request $request){
        $user=auth('api')->user();
        return  $this->userOssTool->getNowSize($user);
    }
    /**
     * 个人文件上传关联
     */
    public function uploadFile(Request $request){
        return $this->userOssTool->uploadFile($request);
    }
}
