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
 * 企业云存储服务接口
 */
interface PersonalOssInterface
{
    public function makeDir($path,User $user);//新建一个目录
    public function deleteDir($path,User $user);//删除一个目录
    public static function makeRootPath(User $user);//生成云存储根目录
    public function removeFileRelation(array $ids,array $model_data);//移除文件与功能模块的关联
    public static function getTargetDirectoryInfo($data);//获取企业云指定目录的信息--目录&文
    public  function updateFileName(Request $request);//更新文件名
    public static function uploadFile(array $filest,array $data);//文件上传
    public static function updateNowSize($user_id,$size,$type);//操作企业当前使用空间
    public static function ossSizeIsEnough(int $user_id,array $files);//计算企业云存储的剩余空间是否满足附件的大小
    public static function copyFileToPath($file_id, $target_directory);//复制文件到指定目录
    public static function deleteFile(int $file_id);//删除某个文件

}