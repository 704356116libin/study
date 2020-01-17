<?php
/**
 * Created by PhpStorm.
 * User: bin
 * Date: 2018/11/14
 * Time: 14:01
 */

namespace App\Interfaces;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * 云存储服务接口
 */
interface UserOssInterface
{
    public function deleteDir($path,User $user);//删除一个目录
    public function makeDir($path,User $user);//新建一个目录
    public function getNowSize(User $user):int;//重新统计云存储的总容量
    public function makeRootPath(User $user);//生成云存储根目录
    public function getDirSize($path,User $user);//获取指定目录下空间大小
    public function alterName($name);//更改网盘名称
    public static function uploadFile(Request $request,array $data);//文件上传
}