<?php
/**
 * Created by PhpStorm.
 * User: bin
 * Date: 2018/11/14
 * Time: 14:01
 */

namespace App\Interfaces;
use App\Models\Company;
use Illuminate\Http\Request;

/**
 * 企业云存储服务接口
 */
interface CompanyOssInterface
{
    public function makeDir($path,Company $company);//新建一个目录
    public function deleteDir($path,Company $company);//删除一个目录
    public function getNowSize(Company $company):int;//重新统计云存储的总容量
    public function makeRootPath(Company $company);//生成云存储根目录
    public function getDirSize($path,Company $company);//获取指定目录下空间大小
    public function alterName($name);//更改网盘名称
    public function removeFileRelation(array $ids,array $model_data);//移除文件与功能模块的关联
    public function getTargetDirectoryInfo(Request $request);//获取企业云指定目录的信息--目录&文
    public  function updateFileName(Request $request);//更新文件名
    public  function getFileBrowseRecord(Request $request);//获取指定文件的浏览记录
    public  function addFileBrowseRecord(Request $request);//添加指定文件的浏览记录

    public static function uploadFile(array $filest,array $data);//文件上传
    public static function updateNowSize($company_id,$size,$type);//操作企业当前使用空间
    public static function ossSizeIsEnough(int $company_id,array $files);//计算企业云存储的剩余空间是否满足附件的大小
    public static function copyFileToPath($file_id, $target_directory,$company_id);//复制文件到指定目录
    public static function deleteFile(int $file_id);//删除某个文件

}