<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;
use App\Interfaces\UserOssInterface;
use App\Models\CompanyNotice;
use App\Models\OssFile;
use App\Models\User;
use App\Models\UserOss;
use App\Repositories\UserOssRepository;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * 个人云存储工具类
 */
class UserOssTool implements UserOssInterface {
    static private $userOssTool;
    private $userOssRepository;

    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
        $this->userOssRepository=UserOssRepository::getUserOssRepository();
    }
    /**
     * 单例模式
     */
    static public function getUserOssTool(){
        if(self::$userOssTool instanceof self)
        {
            return self::$userOssTool;
        }else{
            return self::$userOssTool = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 创建一个目录
     * @param $path
     */
    public function makeDir($path,User $user)
    {
        if(Storage::makeDirectory($user->oss->root_path.$path)){
            return json_encode(['status'=>'success','message'=>'目录创建成功']);
        } else{
            return json_encode(['status'=>'fail','message'=>'目录创建不成功']);
        };
    }
    /**
     * 删除一个目录
     * @param $path
     */
    public function deleteDir($path,User $user)
    {
        if(Storage::deleteDirectory($user->oss->root_path.$path)){
            $this->getNowSize($user);
            return json_encode(['status'=>'success','message'=>'目录删除成功']);
        } else{
            return json_encode(['status'=>'fail','message'=>'目录删除不成功']);
        };
    }
    /**
     * 拿到当前存储总容量
     * @param $rootPath
     */
    public function getNowSize(User $user): int
    {
        $files=Storage::allFiles($user->oss->root_path);
        $size=0;
        foreach ($files as $file){
            $size+=Storage::size($file);
        }
        $this->userOssRepository->updateOss($user->oss->id,['now_size'=>$size]);//更新user_oss存储信息
        return $size;
    }
    /**
     * 生成个人云存储根目录
     * @param $id
     */
    public function makeRootPath(User $user)
    {
        $rootPath=config('oss.user.path').'user'.($user->id).'/';
        if(Storage::makeDirectory($rootPath)){
            UserOss::create([
                'user_id'=>$user->id,
                'root_path'=>$rootPath,
            ]);
            return json_encode(['status'=>'success','message'=>'目录创建成功']);
        }else{
            return json_encode(['status'=>'fail','message'=>'目录创建不成功']);
        };
    }
    /**
     * 获取指定目录下的文件大小
     * @param $path
     */
    public function getDirSize($path,User $user)
    {
        $files=Storage::allFiles($user->oss->root_path.$path);
        $size=0;
        foreach ($files as $file){
            $size+=Storage::size($file);
        }
        $this->userOssRepository->updateOss($user->oss->id,['now_size'=>$size]);//更新user_oss存储信息
        return $size;
    }
    /**
     * 更改网盘名称(暂定)
     * @param $name
     */
    public function alterName($name)
    {
       if(1){

       }else{

       }
    }
    /**
     * 企业文件上传
     * @param Request $request
     * @param array $data:文件上传的oss参数
     */
    public static function uploadFile(Request $request,array $data)
    {
        /**
         * 验证是否有企业文件上传的权限
         */
        if(!true){
            return ['您没有上传文件的权限'];
        }
        //获取所有的文件
        $files=$request->get(config('filesystems.upload.up_files_name'));
        if(count($files)==0){
            return ['status'=>'fail','message'=>'没有可上传的文件'];
        }
        $error_message=[];//文件上传错误信息
        $regex=config('filesystems.upload.allow_file_type');//能够上传的文件格式
        $name=config('filesystems.upload.file.name');//文件name属性
        $size=config('filesystems.upload.file.size');//文件size属性
        $real_path=config('filesystems.upload.file.real_path');//文件real_path属性
        $max_size=config('filesystems.upload.max_size');//单文件上传阈值
        //开始上传文件
        foreach ($files as $file){
            //文件格式大小合法性校验
            if(preg_match($regex,$file['name'])==0){
                $error_message[]=$file[$name].':格式不合法';
                continue;
            }elseif ($file[$size]>$max_size*1024*1024){
                $error_message[]=$file[$name].':大小超出'.$max_size.'M' ;
                continue;
            }
            //进行文件存储&添加文件关联
            $state=self::strogeOssFile(['name'=>$file['name'],'real_path'=>$file['thumbUrl'],'size'=>$file['size']],$data);
            if(!$state){
                $error_message[]=$file[$name].':上传出错' ;
            }
        }
        return $error_message;
    }
    /**
     * 企业单个文件上传关联
     * @param $file_data:文件上传所需要的文件信息
     * @param $data:文件关联模型需要的信息,(向上追溯)[
     *    'oss_path'=>$company->oss->root_path.'/notice',//公告上传的云路径,其他模块与之类似
    'model_id'=>$notice->id,//关联模型的id
    'model_type'=>CompanyNotice::class,//关联模型的类名
    'company_id'=>$company_id,//所属公司的id
    'uploader_id'=>$user->id,//上传者的id
     * ]
     * @return string
     */
    public static function strogeOssFile( array $file_data,$data)
    {
        $filename=$file_data['name'];//取出文件名
        $realpath=$file_data['real_path'];//取出文件临时路径
        $size=round($file_data['size']/1024,2);//文件的大小/kb
        $origin_name=$file_data['name'];//原始文件全名
        $extenName=FunctionTool::get_file_extension_name($filename);//文件扩展名
        $filename =   FunctionTool::encrypt_id($data['model_id']).str_random(8).'.' . $extenName;//存放的文件名
        DB::beginTransaction();
        try{
            Storage::disk('oss')->put($data['oss_path'].'/'.$filename,
                file_get_contents($realpath));
            //企业云盘空间累加空间
//            self::updateNowSize($data['company_id'],$size,'add');//操作now_size
            //文件oss路径映射关系
            $file=OssFile::create(['name'=>$origin_name,
                'company_id'=>$data['company_id'],
                'uploader_id'=>$data['uploader_id'],
                'size'=>$size,
                'oss_path'=>$data['oss_path'].'/'.$filename,
            ]);
            //文件与model多态关系
            DB::table('model_has_file')->insert(['model_id'=>$data['model_id'],'model_type'=>$data['model_type'],'file_id'=>$file->id]);
            DB::commit();
            return  true;
        }catch (\Exception $e){
            dd($e);
            DB::rollBack();
            return false ;
        }
    }
}