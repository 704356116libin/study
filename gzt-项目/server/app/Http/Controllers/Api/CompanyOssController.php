<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Tools\CompanyOssTool;
use App\Tools\FunctionTool;
use App\Tools\UserOssTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

/**
 * 企业云存储控制器
 * Class OssController
 * @package App\Http\Controllers\Api
 */
class CompanyOssController extends Controller
{
    private $companyOssTool;
    /**
     * UserOssController constructor.
     */
    public function __construct()
    {
        $this->companyOssTool=CompanyOssTool::getCompanyOssTool();
    }
    /**
     * 创建一个目录
     * @param $path
     */
    public function makeDir(Request $request)
    {
        return  $this->companyOssTool->makeDir($request->path,Company::find(FunctionTool::decrypt_id($request->company_id)));
    }
    /**
     * 删除一个目录
     * @param $path
     */
    public function deleteDir(Request $request)
    {
        return  $this->companyOssTool->deleteDir($request->path,Company::find(FunctionTool::decrypt_id($request->company_id)));
    }
    /**
     * 拿到当前存储总容量
     * @param $rootPath
     */
    public function getNowSize(Request $request){
        if(!is_null($request->company_id)){
            return  $this->companyOssTool->getNowSize(Company::find(FunctionTool::decrypt_id($request->company_id)));
        }else{
            return json_encode(['status'=>'fail','message'=>'缺少参数']);
        }
    }
    /**
     * 企业文件直接上传--前端配合传路径
     */
    public function uploadFile(Request $request){
        $files=$_FILES;
        $user=auth('api')->user();
        $company=Company::find($request->company_id);
        return $this->companyOssTool->uploadFile($files,[
            'oss_path'=>$company->oss->root_path.$request->get('target_directory',''),//公告上传的云路径,其他模块与之类似
            'model_id'=>null,//关联模型的id
            'model_type'=>null,//关联模型的类名
            'company_id'=>$company->id,//所属公司的id
            'uploader_id'=>$user->id,//上传者的id
        ]);
    }
    /**
     * 企业工共文件直接上传--前端配合传路径
     */
    public function uploadPublicFile(Request $request){
        $user=auth('api')->user();
        $company=Company::find(FunctionTool::decrypt_id($request->company_id));
        $files = $_FILES;
        $target_directory=$request->get('path','')==='/'?'public':'public/'.substr($request->get('path',''),0,strlen($request->get('path',''))-1);
        $data= $this->companyOssTool->uploadFile($files,[
            'oss_path'=>$company->oss->root_path.$target_directory,//公告上传的云路径,其他模块与之类似
            'model_id'=>$company->id,//关联模型的id
            'model_type'=>'public',//关联模型的类名
            'company_id'=>$company->id,//所属公司的id
            'uploader_id'=>$user->id,//上传者的id
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
    public  function copyFileToPath(Request $request){

        if($request->type=='company'){
            return CompanyOssTool::copyFileToPath(FunctionTool::decrypt_id($request->file_id),$request->target_directory,$request->company_id);
        }else{
            return CompanyOssTool::copyFileToPersonal(FunctionTool::decrypt_id($request->file_id),$request->target_directory);
        }
    }
    /**
     * 复制文件夹
     */
    public function copyFolder(Request $request)
    {
        return $this->companyOssTool->copyFolder($request->all());
    }
    /**
     * 获取企业云指定目录的信息--目录&文件
     */
    public function getTargetDirectoryInfo(Request $request){
        return $this->companyOssTool->getTargetDirectoryInfo($request);
    }
    /**
     * 更改文件名
     */
    public  function updateFileName(Request $request){
        return $this->companyOssTool->updateFileName($request);
    }
    /**
     * 移除文件,
     * @request所需参数file_id
     */
    public  function deleteFile(Request $request)
    {
        return CompanyOssTool::deleteFile(FunctionTool::decrypt_id($request->file_id));
    }
    /**
     * 转移文件
     */
    public function moveFile(Request $request)
    {
        return $this->companyOssTool->moveFile($request->all());
    }
    /**
     * 移动文件夹
     */
    public function moveFolder(Request $request)
    {
        return $this->companyOssTool->moveFolder($request->all());
    }
    /**
     * 添加指定文件的浏览记录
     * request 中需要file_id--文件id,type--文件操作类型
     * 返回
     */
    public  function addFileBrowseRecord(Request $request){
        return $this->companyOssTool->addFileBrowseRecord($request);
    }
    /**
     * 获取指定文件的浏览记录
     * request 中需要file_id--目标文件id,now_page--当前页数,page_size--每页大小
     * 返回
     */
    public  function getFileBrowseRecord(Request $request){
        return $this->companyOssTool->getFileBrowseRecord($request);
    }
    /**
     * 批量删除
     */
    public function batchDelete(Request $request)
    {
        return $this->companyOssTool->batchDelete($request->dirs,$request->fileIds,$request->company_id);
    }
    /**
     * 批量复制
     */
    public function batchCopy(Request $request)
    {
        return $this->companyOssTool->batchCopy($request->dirs,$request->fileIds,$request->type,$request->target_directory,$request->company_id,$request->from_company_id);
    }
    /**
     * 批量移动
     */
    public function batchMove(Request $request)
    {
        return $this->companyOssTool->batchMove($request->file_ids,$request->target_directory,$request->dirs,$request->type,$request->company_id);
    }
}
