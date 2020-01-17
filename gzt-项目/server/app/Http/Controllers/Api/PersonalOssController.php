<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Tools\FunctionTool;
use App\Tools\PersonalOssTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 企业云存储控制器
 * Class OssController
 * @package App\Http\Controllers\Api
 */
class PersonalOssController extends Controller
{
    private $personalOssTool;

    /**
     * UserOssController constructor.
     */
    public function __construct()
    {
        $this->personalOssTool = PersonalOssTool::getPersonalOssTool();
    }

    /**
     * 创建一个目录
     * @param $path
     */
    public function makeDir(Request $request)
    {
        return $this->personalOssTool->makeDir($request->path, auth('api')->user());
    }

    /**
     * 删除一个目录
     * @param $path
     */
    public function deleteDir(Request $request)
    {
        return $this->personalOssTool->deleteDir($request->path, auth('api')->user());
    }

    /**
     * 企业文件直接上传--前端配合传路径
     */
    public function uploadFile(Request $request)
    {
        $user = auth('api')->user();
        $files = [['name' => $_FILES['file']['name'], 'tmp_name' => $_FILES['file']['tmp_name'], 'size' => $_FILES['file']['size']]];
        $data=$this->personalOssTool->uploadFile($files, [
            'oss_path' => $user->oss->root_path . $request->get('dir'),//上传的云路径
            'model_id' => $user->id,//关联模型的id
            'model_type' => User::class,//关联模型的类名
            'user_id' => $user->id,//所属用户的id
            'uploader_id' => $user->id,//上传者的id
        ]);
        if($data===true){
            return ['status'=>'success','message'=>'上传成功'];
        }else{
            return ['status'=>'fail','message'=>'上传失败'];
        }

    }

    /**
     * 文件复制到指定目录
     */
    public function copyFileToPath(Request $request)
    {
        if($request->type=='personal'){
            return PersonalOssTool::copyFileToPath($request->file_id, $request->target_directory);
        }else{
            return PersonalOssTool::copyFileToCompany($request->file_id, $request->target_directory,$request->company_id);
        }
    }
    /**
     * 复制文件夹
     */
    public function copyFolder(Request $request)
    {
        return $this->personalOssTool->copyFolder($request->all());
    }

    /**
     * 获取企业云指定目录的信息--目录&文件
     */
    public function getTargetDirectoryInfo(Request $request)
    {
        return $this->personalOssTool->getTargetDirectoryInfo($request->all());
    }

    /**
     * 更改文件名
     */
    public function updateFileName(Request $request)
    {
        return $this->personalOssTool->updateFileName($request);
    }

    /**
     * 移除文件,
     * @request所需参数file_id
     */
    public function deleteFile(Request $request)
    {
        return PersonalOssTool::deleteFile(FunctionTool::decrypt_id($request->file_id));
    }
    /**
     * 转移文件
     */
    public function moveFile(Request $request)
    {
        return $this->personalOssTool->moveFile($request->all());
    }
    /**
     * 移动文件夹
     */
    public function moveFolder(Request $request)
    {
        return $this->personalOssTool->moveFolder($request->all());
    }
    /**
     * 纯文件文件下载
     */
    public function singleFileUpload(Request $request)
    {
        return $this->personalOssTool->singleFileUpload(FunctionTool::decrypt_id_array($request->fileIds),$request->type,$request->company_id);
    }
    /**
     * 打包下载
     */
    public function downloadPackage(Request $request)
    {
        return $this->personalOssTool->downloadPackage($request->all());
    }
    /**
     * 文件动态
     */
    public function fileDynamics()
    {
        return $this->personalOssTool->fileDynamics();
    }
    /**
     * 最近使用
     */
    public function recentlyUsed()
    {
        return $this->personalOssTool->recentlyUsed();
    }
    /**
     * 批量删除
     */
    public function batchDelete(Request $request)
    {
        return $this->personalOssTool->batchDelete($request->dirs,$request->fileIds);
    }
    /**
     * 批量复制
     */
    public function batchCopy(Request $request)
    {
        return $this->personalOssTool->batchCopy($request->dirs,$request->fileIds,$request->type,$request->target_directory,$request->company_id);
    }
    /**
     * 批量移动
     */
    public function batchMove(Request $request)
    {
        return $this->personalOssTool->batchMove($request->fileIds,$request->target_directory,$request->dirs,$request->type,$request->company_id);
    }

}
