<?php
namespace App\Repositories;
use App\Models\OssFile;
use App\Models\PersonalHasFile;
use App\Models\PersonalOss;
use App\Models\PersonalOssFile;
use Illuminate\Support\Facades\DB;


/**
 * 文件相关仓库类
 * Created by PhpStorm.
 * User: bin
 * Date: 2018/10/29
 * Time: 13:58
 */

class OssFileRepository
{
    static private $ossFileRepository;
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {

    }
    /**
     * 单例模式
     */
    static public function getOssFileRepository(){
        if(self::$ossFileRepository instanceof self)
        {
            return self::$ossFileRepository;
        }else{
            return self::$ossFileRepository = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 移除相应模块相应file的关联信息
     * @param array $data
     */
    public function removeFileRelation(array $ids,array $mode_data)
    {
        DB::table('model_has_file')
            ->whereIn('file_id',$ids)
            ->where('model_id',$mode_data['model_id'])
            ->where('model_type',$mode_data['model_type'])
            ->delete();
    }

    /**
     * 拿到指定目录下的file对象
     */
    public static function getFilesByDirectory($path){
        return OssFile::where('oss_path','like',$path.'%')
                   ->where('oss_path','not like',$path.'%/%')
                   ->get();
    }
    /**
     * 拿到指定目录下的file对象(个人空间)
     */
    public static function getPersonalFilesByDirectory($path){
        return PersonalOssFile::where('oss_path','like',$path.'%')
            ->where('oss_path','not like',$path.'%/%')
            ->get();
    }
    /**
     * 更新文件信息
     */
    public static function updateFile(int $file_id,$directory,$name){
        //写入操作记录
        $user=auth('api')->user();
        $record=[
            'company_id'=>$user->current_company_id,
            'user_id'=>$user->id,
            'content'=>'',
            'type'=>'修改文件名',
            'size'=>null,
            'file_name'=>$name,
            'dir'=>$directory,
            'created_at'=>date('Y-m-d H:i:s',time()),
        ];
        DB::table('company_oss_record')->insert($record);
        return OssFile::where('id',$file_id)
                        ->update(['name'=>$name]);
    }
    /**
     * 更新文件信息(个人)
     */
    public static function updatePersonalFile(int $file_id,$directory,$name){
        //写入操作记录
        $record=[
            'user_id'=>auth('api')->id(),
            'content'=>'',
            'type'=>'修改文件名',
            'size'=>null,
            'file_name'=>$name,
            'dir'=>$directory,
            'created_at'=>date('Y-m-d H:i:s',time()),
        ];
        DB::table('company_oss_record')->insert($record);
        return PersonalOssFile::where('id',$file_id)
            ->update(['name'=>$name]);
    }
    /**
     *查询指定目录下是否存在某个名称的文件
     */
    public static function directoryExsitFile(string $file_name,string $directory){
        $count=OssFile::where('name',$file_name)
            ->where('oss_path','regexp',$directory.'.*\..*')
            ->count();
        return $count==0?false:true;
    }
    /**
     *查询指定目录下是否存在某个名称的文件(个人)
     */
    public static function directoryExsitPersonalFile(string $file_name,string $directory){
        $count=PersonalOssFile::where('name',$file_name)
            ->where('oss_path','regexp',$directory.'.*\..*')
            ->count();
        return $count==0?false:true;
    }
    /**
     *查询指定目录下是否有文件
     */
    public static function directoryHasFile(string $directory){
        $file=OssFile::where('oss_path','regexp',$directory.'.*\..*');
        $ids=$file->pluck('id');
        DB::table('model_has_file')->whereIn('file_id',$ids)->delete();
        $file->delete();
        return false;
    }
    /**
     *查询指定目录下是否有文件(个人)
     */
    public static function directoryHasPersonalFile(string $directory){
        $file=PersonalOssFile::where('oss_path','regexp',$directory.'.*\..*');
        $ids=$file->pluck('id');
        DB::table('personal_has_file')->whereIn('file_id',$ids)->delete();
        $file->delete();
        return false;
    }

    /**
     * 获取某个文件的浏览记录,按时间倒叙--分页
     * @param int $offset:偏移量
     * @param int $limit:每页大小
     * @param int $file_id:目标文件id
     */
    public static function getFileBrowseRecord(int $offset,int $limit ,int $file_id){
        $records = DB::table('oss_file_browse_record')
            ->where('file_id', $file_id)
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->toArray();
        //数据总数,用于分页展示
        $count=DB::table('oss_file_browse_record')
            ->where('file_id', $file_id)
            ->count();
        return '';
    }
}