<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;

use App\Http\Resources\FileResource;
use App\Interfaces\PersonalOssInterface;
use App\Models\Company;
use App\Models\CompanyOss;
use App\Models\FileUseRecord;
use App\Models\OssFile;
use App\Models\OssRecord;
use App\Models\PersonalOss;
use App\Models\PersonalOssFile;
use App\Models\User;
use App\Repositories\CompanyOssRepository;
use App\Repositories\OssFileRepository;
use Carbon\Carbon;
use Chumper\Zipper\Facades\Zipper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use OSS\OssClient;
use PhpOffice\PhpWord\IOFactory;
/**
 * 个人云存储工具类
 */
class PersonalOssTool implements PersonalOssInterface
{
    static private $personalOssTool;
    private $companyOssRepository;
    private $ossFileRepository;
    private $validateTool;//数据验证类

    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
        $this->companyOssRepository = CompanyOssRepository::getCompanyOssRepository();
        $this->ossFileRepository = OssFileRepository::getOssFileRepository();
        $this->validateTool = ValidateTool::getValidateTool();
    }

    /**
     * 单例模式
     */
    static public function getPersonalOssTool()
    {
        if (self::$personalOssTool instanceof self) {
            return self::$personalOssTool;
        } else {
            return self::$personalOssTool = new self;
        }
    }

    /**
     * 防止被克隆
     */
    private function _clone()
    {
    }

    /**
     * 创建一个目录
     * @param $path
     */
    public function makeDir($path, User $user)
    {
        //是否有网盘管理权限的判断
        if (!true) {
            json_encode(['status' => 'fail', 'message' => '没有操作权限']);
        }
        if (Storage::makeDirectory($user->oss->root_path . $path)) {
            //写入操作记录
            $record = [
                'user_id' => auth('api')->id(),
                'content' => '',
                'type' => '创建目录',
                'size' => null,
                'file_name' => null,
                'dir' => $path,
                'created_at' => date('Y-m-d H:i:s', time()),
            ];
            DB::table('company_oss_record')->insert($record);
            return json_encode(['status' => 'success', 'message' => '目录创建成功']);
        } else {
            return json_encode(['status' => 'fail', 'message' => '目录创建不成功']);
        };
    }

    /**
     * 删除一个目录
     * @param $path
     */
    public function deleteDir($path, User $user)
    {
        //是否有网盘管理权限的判断(个人网盘不考虑这点)
        if (!true) {
            return json_encode(['status' => 'fail', 'message' => '没有操作权限']);
        }
        //更新磁盘大小
        $size = $this->getSizeByDir($user->oss->root_path . $path);
        self::updateNowSize($user->id, $size, 'sub');
        //查询当前目录下是否有文件
        if (OssFileRepository::directoryHasPersonalFile($user->oss->root_path . $path)) {
            return json_encode(['status' => 'fail', 'message' => '当前目录下有文件不能删除']);
        }
        //删除目录
        if (Storage::deleteDirectory($user->oss->root_path . $path)) {
            //写入操作记录
            $content = '你在' . date('Y-m-d H:i:s', time()) . '将' . $user->oss->root_path . $path . '的目录删除';
            $record = [
                'user_id' => auth('api')->id(),
                'content' => $content,
                'type' => '删除目录',
                'size' => null,
                'file_name' => null,
                'dir' => $path,
                'created_at' => date('Y-m-d H:i:s', time()),
            ];
            DB::table('company_oss_record')->insert($record);
            return json_encode(['status' => 'success', 'message' => '目录删除成功']);
        } else {
            return json_encode(['status' => 'fail', 'message' => '目录删除不成功']);
        };
    }

    /**
     * 获取企业云指定目录的信息--目录&文件
     */
    public static function getTargetDirectoryInfo($request)
    {
        $user_id = auth('api')->id();
        //返回的信息格式
        $data = ['directories' => [], 'files' => [],];
        $user_id = array_get($request, 'user_id') === null ? $user_id : FunctionTool::decrypt_id($request['user_id']);
        $root_path = User::find($user_id)->oss->root_path;
        $target_directory = $root_path . array_get($request, 'target_directory');
        //目录递归查找
        $directories = Storage::disk('oss')->directories($target_directory);

        $dirs = [];
        $pattern = '/^' . preg_replace('/"/', '', json_encode($target_directory)) . '([\s\S]*)$/';
        foreach ($directories as $v) {
            if ($v === $root_path . 'avatar') {
                continue;
            }
            preg_match($pattern, $v, $matches);
            $allSize = self::getSizeByDir($v);
            $dirs[] = ['name' => $matches[1], 'size' => $allSize, 'dir' => $v, 'type' => 'folder'];
        }
        $data['directories'] = $dirs;//获取目录信息
        $data['files'] = FileResource::collection(OssFileRepository::getPersonalFilesByDirectory($target_directory))->toArray(1);
        return json_encode(['status' => 'success', 'data' => $data]);
    }

    /**
     * 获取目录大小
     */
    public static function getSizeByDir($dir)
    {
        $allFiles = Storage::disk('oss')->allFiles($dir);
        $size = 0;
        foreach ($allFiles as $file) {
            $size = $size + Storage::disk('oss')->size($file);
        }
        return $size;
    }

    /**
     * 生成个人云存储根目录(在用户注册的时候调用)
     * @param $id
     */
    public static function makeRootPath(User $user)
    {
        $rootPath = config('oss.user.path') . 'user' . ($user->id) . '/';
        if (Storage::makeDirectory($rootPath)) {
            PersonalOss::create([
                'user_id' => $user->id,
                'root_path' => $rootPath,
                'name' => '个人网盘',
                'all_size' => config('oss.user.default_size') * 1024 * 1024,//单位/kb 默认个人存储空间2g
            ]);
            return json_encode(['status' => 'success', 'message' => '目录创建成功']);
        } else {
            return json_encode(['status' => 'fail', 'message' => '目录创建不成功']);
        };
    }

    /**
     * 更新文件名
     * request所需参数:name-文件名(包含扩展名),directory-当前目录名,file_id--文件id
     */
    public function updateFileName(Request $request)
    {
//        $this->link();
        $name = $request->name;
        $directory = $request->directory == '' ? auth('api')->user()->oss->root_path : $request->directory;
        //验证是否有文件管理权
        if (!true) {
            return ['您没有文件管理权'];
        }
        //文件名合法性校验
        $validator = $this->validateTool->sensitive_word_validate(['name' => $name]);
        if (is_array($validator)) {
            $validator['index'] = 'name';
            return json_encode($validator);
        }
        //查看当前目录是否有重名文件
        if (OssFileRepository::directoryExsitPersonalFile($name, $directory)) {
            return json_encode(['status' => 'fail', 'message' => '当前目录有重名文件']);
        }
        //数据更改
        OssFileRepository::updatePersonalFile(FunctionTool::decrypt_id($request->file_id), $directory, $name);
        return json_encode(['status' => 'success', 'message' => '文件更新成功!']);
    }

    /**
     * 个人文件上传
     * @param Request $request
     * @param array $data :文件上传的oss参数
     */
    public static function uploadFile(array $files, array $data)
    {
        //获取所有的文件
        if (count($files) == 0) {
            return ['status' => 'fail', 'message' => '没有可上传的文件'];
        }
        $error_message = [];//文件上传错误信息
        $name = config('filesystems.upload.file.name');//文件name属性
        //开始上传文件
        foreach ($files as $file) {
            //进行文件存储&添加文件关联
            $state = self::strogeOssFile(['name' => $file['name'], 'real_path' => $file['tmp_name'], 'size' => $file['size']], $data);
            if (!$state) {
                $error_message[] = $file[$name] . ' 上传出错';
            }
        }
        return count($error_message) == 0 ? true : implode(',', $error_message);
    }

    /**
     * 企业单个文件上传关联
     * @param $file_data :文件上传所需要的文件信息
     * @param $data :文件关联模型需要的信息,(向上追溯)[
     *    'oss_path'=>$company->oss->root_path.'/notice',//公告上传的云路径,其他模块与之类似
     * 'model_id'=>$notice->id,//关联模型的id
     * 'model_type'=>CompanyNotice::class,//关联模型的类名
     * 'company_id'=>$company_id,//所属公司的id
     * 'uploader_id'=>$user->id,//上传者的id
     * ]
     * @return string
     */
    public static function strogeOssFile(array $file_data, $data)
    {
        $user = auth('api')->user();

        $filename = $file_data['name'];//取出文件名
        $realpath = $file_data['real_path'];//取出文件临时路径
        $size = round($file_data['size'] / 1024, 2);//文件的大小/kb
        $origin_name = $file_data['name'];//原始文件全名
        $extenName = FunctionTool::get_file_extension_name($filename);//文件扩展名
        $filename = FunctionTool::encrypt_id($data['model_id']) . str_random(8) . '.' . $extenName;//存放的文件名
        //验证云盘空间大小
        $message = self::ossSizeIsEnough($user->id, [0 => ['name' => $filename, 'size' => $size]]);
        if (count($message) != 0) {
            return json_encode(['status' => 'fail', 'message' => implode(',', $message)]);
        }
        DB::beginTransaction();
        try {
            Storage::disk('oss')->put($data['oss_path'] . $filename,
                file_get_contents($realpath));
            //写入操作记录
            $dir = str_replace(auth('api')->user()->oss->root_path, '', $data['oss_path']);
            $record = [
                'user_id' => auth('api')->id(),
                'content' => '',
                'type' => '上传文件',
                'size' => $size,
                'file_name' => $origin_name,
                'dir' => $dir,
                'created_at' => date('Y-m-d H:i:s', time()),
            ];
            DB::table('company_oss_record')->insert($record);
            //企业云盘空间累加空间--更新企业云存储空间
            self::updateNowSize($data['user_id'], $size, 'add');
            //文件oss路径映射关系
            $file = PersonalOssFile::create(['name' => $origin_name,
                'user_id' => $data['user_id'],
                'uploader_id' => $data['uploader_id'],
                'size' => $size,
                'oss_path' => $data['oss_path'] . $filename,
            ]);
            //文件与model多态关系--$data['model_id']==null时表示不进行多态文件关联
            if ($data['model_id'] != null) {
                DB::table('personal_has_file')->insert(['model_id' => $data['model_id'], 'model_type' => $data['model_type'], 'file_id' => $file->id]);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return false;
        }
    }

    /**
     * 移除相应模块相应file的关联信息(目前云存储文件的信息删除不了)
     * @param array $ids :需要移除关系的目标文件id
     * @param array $model_data :['model_id'=>'model_id','model_type'=>'模型类名']
     */
    public function removeFileRelation(array $ids, array $model_data)
    {
        //若ids数组为空数组则不做任何处理
        if (count($ids) == 0) return;
        $ids = FunctionTool::decrypt_id_array($ids);//解析加密数据(array类型)
        return $this->ossFileRepository->removeFileRelation($ids, $model_data);
    }

    /**
     * 计算企业云存储的剩余空间是否满足附件的大小
     */
    public static function ossSizeIsEnough(int $user_id, array $files)
    {
        $error_message = [];//文件上传错误信息
        $regex = config('filesystems.upload.allow_file_type');//能够上传的文件格式
        $name = config('filesystems.upload.file.name');//文件name属性
        $size = config('filesystems.upload.file.size');//文件size属性
        $max_size = config('filesystems.upload.max_size');//单文件大小阈值
        $all_size = 0;//单位为kb
        //文件格式大小合法性校验
        foreach ($files as $file) {
            $all_size += $file[$size];//统计本次文件上传的总大小
            if (preg_match($regex, $file[$name]) == 0) {
                $error_message[] = $file[$name] . ':格式不合法';
                continue;
            } elseif ($file[$size] > $max_size * 1024 * 1024) {
                $error_message[] = $file[$name] . ':大小超出' . $max_size . 'M';
                continue;
            }
        }
        //若之前的文件合法性校验通过的话,则进行oss空间大小的判断
        $c_oss = User::find($user_id)->oss;//获取企业对应的oss实例
        if ($c_oss->now_size + $all_size > $c_oss->all_size) {
            $error_message[] = '云盘空间不足';
        }
        return $error_message;
    }

    /**
     * 文件复制到指定目录(个人内部复制)
     */
    public static function copyFileToPath($file_id, $target_directory)
    {
        try {
            DB::beginTransaction();
            $file_id = FunctionTool::decrypt_id($file_id);
            $user_id = auth('api')->id();
            //验证是否有文件管理权
            if (!true) {
                return ['您没有文件管理权'];
            }
            //获取目标文件
            $file = PersonalOssFile::find($file_id);
            //判断空间大小是否支持文件复制(需要将单个file变为数组)并接收返回信息
            $message = self::ossSizeIsEnough($file->user_id, [0 => ['name' => $file->name, 'size' => $file->size]]);
            if (count($message) != 0) {
                return json_encode(['status' => 'fail', 'message' => implode(',', $message)]);
            }
            //截取文件的扩展名--在$matches[1]中
            preg_match(config('regex.file_extension'), $file->name, $matches);
            //复制文件至指定路径
            $to_path = User::find($file->user_id)->oss->root_path . $target_directory .
                FunctionTool::encrypt_id($file->id) . str_random(8) . '.' . $matches[1];
            Storage::disk('oss')->copy($file->oss_path, $to_path);
            //创建新的文件
            $personalOssFile = PersonalOssFile::create([
                'uploader_id' => $user_id,
                'user_id' => $file->user_id,
                'size' => $file->size,
                'name' => $file->name,
                'oss_path' => $to_path,//新文件路径
            ]);
            //更新企业云oss空间
            self::updateNowSize($file->user_id, $file->size, 'add');
            DB::table('personal_has_file')
                ->where('model_id', $user_id)
                ->where('model_type', '=', User::class)
                ->insert([
                    'file_id' => $personalOssFile->id,
                    'model_id' => $user_id,
                    'model_type' => User::class,
                ]);
            DB::commit();
            return json_encode(['status' => 'success', 'message' => '文件复制成功']);
        } catch (\Exception $exception) {
            DB::rollBack();
            dd($exception);
            return json_encode(['status' => 'fail', 'message' => '服务器错误']);
        }
    }

    /**
     * 文件复制到指定目录(个人内部文件复制到公司)
     */
    public static function copyFileToCompany($file_id, $target_directory, $company_id)
    {
        try {
            DB::beginTransaction();
            $company_id = FunctionTool::decrypt_id($company_id);
            $file_id = FunctionTool::decrypt_id($file_id);
            //验证是否有文件管理权
            if (!true) {
                return ['您没有文件管理权'];
            }
            //获取目标文件
            $file = PersonalOssFile::find($file_id);
            //判断空间大小是否支持文件复制(需要将单个file变为数组)并接收返回信息
            $message = CompanyOssTool::ossSizeIsEnough($company_id, [0 => ['name' => $file->name, 'size' => $file->size]]);
            if (count($message) != 0) {
                return json_encode(['status' => 'fail', 'message' => implode(',', $message)]);
            }
            //截取文件的扩展名--在$matches[1]中
            preg_match(config('regex.file_extension'), $file->name, $matches);
            //复制文件至指定路径
            $to_path = Company::find($company_id)->oss->root_path . 'public/' . $target_directory .
                FunctionTool::encrypt_id($file->id) . str_random(8) . '.' . $matches[1];
            Storage::disk('oss')->copy($file->oss_path, $to_path);
            //创建新的文件
            $personalOssFile = OssFile::create([
                'uploader_id' => $file->user_id,
                'company_id' => $company_id,
                'size' => $file->size,
                'name' => $file->name,
                'oss_path' => $to_path,//新文件路径
            ]);
            //更新企业云oss空间
            CompanyOssTool::updateNowSize($company_id, $file->size, 'add');
            DB::table('model_has_file')
                ->insert([
                    'file_id' => $personalOssFile->id,
                    'model_id' => $company_id,
                    'model_type' => 'public',
                ]);
            DB::commit();
            return json_encode(['status' => 'success', 'message' => '文件复制成功']);
        } catch (\Exception $exception) {
            DB::rollBack();
            dd($exception);
            return json_encode(['status' => 'fail', 'message' => '服务器错误']);
        }
    }

    /**
     * 复制文件夹
     */
    public function copyFolder($data)
    {
        $type = $data['type'];
        $dirs = $data['dirs'];
        $target_dir = $data['target_directory'];
        //目录下的文件
        $user = auth('api')->user();
        foreach ($dirs as $ml) {
            $dir = User::find($user->id)->oss->root_path . $ml;
            //获取该文件夹下所有文件真实路径
            $files_oss_path = Storage::disk('oss')->allFiles($dir);
            foreach ($files_oss_path as $oss_path) {
                //拼接要复制到的目标目录
                $explodeDir = explode($ml, $oss_path);
                $explodeDir = $explodeDir[count($explodeDir) - 1];
                $file_name = explode('/', $oss_path);
                $file_name = $file_name[count($file_name) - 1];
                $target_directory_pj = $target_dir . $ml . $explodeDir;
                $target_directory = str_replace($file_name, '', $target_directory_pj);
                $file_id = PersonalOssFile::where('oss_path', $oss_path)->value('id');
                //如果为找到对应文件,则跳出本次循环
                if ($file_id === null) {
                    continue;
                }
                if ($type === 'personal') {//复制到个人磁盘下
                    $jg = self::copyFileToPath(FunctionTool::encrypt_id($file_id), $target_directory);
                } else {//复制到公司磁盘下
                    $jg = self::copyFileToCompany(FunctionTool::encrypt_id($file_id), $target_directory, $data['company_id']);
                }

                if (json_decode($jg)->status === 'fail') {
                    return ['status' => 'fail', 'message' => '服务器错误'];
                }
            }
        }
        return ['status' => 'success', 'message' => '操作成功'];
    }

    /**
     * 移除文件,
     * @param int $file_id
     */
    public static function deleteFile(int $file_id)
    {
        //验证是否有文件管理权
        if (!true) {
            return json_encode(['status' => 'fail', 'message' => '您没有文件管理权']);
        }
        //获取文件对象
        $file = PersonalOssFile::find($file_id);
        //写入操作记录
        $record = [
            'user_id' => auth('api')->id(),
            'content' => '',
            'type' => '删除文件',
            'size' => $file->size,
            'file_name' => $file->name,
            'dir' => null,
            'created_at' => date('Y-m-d H:i:s', time()),
        ];
        DB::table('company_oss_record')->insert($record);
        //删除阿里云文件
        $state = Storage::disk('oss')->delete($file->oss_path);
        if ($state) {
            //删除文件
            $file->delete();
            //删除文件关联
            DB::table('personal_has_file')
                ->where('file_id', $file_id)
                ->delete();
            //更新企业云存储空间
            self::updateNowSize($file->user_id, $file->size, 'sub');
            return json_encode(['status' => 'success', 'message' => '文件删除成功']);
        } else {
            return json_encode(['status' => 'fail', 'message' => '文件删除失败']);
        }
    }

    /**
     * 操作个人当前存储空间--个人云盘空间大小的操作(只改变)
     * @param $user_id :用户id
     * @param $size :文件大小/kb
     * @param $type :add--表示累加,--sub表示减
     */
    public static function updateNowSize($user_id, $size, $type)
    {
        if ($type == 'add') {
            DB::table('personal_oss')->where('user_id', $user_id)->increment('now_size', $size);
        } elseif ($type == 'sub') {
            DB::table('personal_oss')->where('user_id', $user_id)->decrement('now_size', $size);
        }
        return true;
    }

    /**
     * 转移文件
     */
    public function moveFile($data)
    {
        $file_id = $data['file_id'];
        $newPath = $data['target_directory'];

        try {
            DB::beginTransaction();
            if(!$newPath){
                return ['请选择转移到的位置'];
            }
            $file_id = FunctionTool::decrypt_id($file_id);
            //验证是否有文件管理权
            if (!true) {
                return ['您没有文件管理权'];
            }
            //获取目标文件
            $file = PersonalOssFile::find($file_id);
            //判断空间大小是否支持文件复制(需要将单个file变为数组)并接收返回信息
            $message = self::ossSizeIsEnough($file->user_id, [0 => ['name' => $file->name, 'size' => $file->size]]);
            if (count($message) != 0) {
                return json_encode(['status' => 'fail', 'message' => implode(',', $message)]);
            }
            //截取文件的扩展名--在$matches[1]中
            preg_match(config('regex.file_extension'), $file->name, $matches);
            //复制文件至指定路径
            $to_path = User::find($file->user_id)->oss->root_path . $newPath .
                FunctionTool::encrypt_id($file->id) . str_random(8) . '.' . $matches[1];
            Storage::disk('oss')->move($file->oss_path, $to_path);
            //创建新的文件
            PersonalOssFile::where('id', $file_id)
                ->update([
                    'oss_path' => $to_path,//新文件路径
                ]);
            //更新企业云oss空间
//            self::updateNowSize($file->user_id, $file->size, 'add');
//            DB::table('personal_has_file')
//                ->where('model_id', $user_id)
//                ->where('model_type', '=', User::class)
//                ->insert([
//                    'file_id' => $personalOssFile->id,
//                    'model_id' => $user_id,
//                    'model_type' => User::class,
//                ]);
            DB::commit();
            return json_encode(['status' => 'success', 'message' => '文件移动成功']);
        } catch (\Exception $exception) {
            DB::rollBack();
            dd($exception);
            return json_encode(['status' => 'fail', 'message' => '服务器错误']);
        }
    }

    /**
     * 移动文件夹
     */
    public function moveFolder($data)
    {
        $dirs = $data['dirs'];
        foreach ($dirs as $dir) {
            $status = $this->copyFolder($data)['status'];
            if ($status === 'success') {
                $user = auth('api')->user();
                //删除原目录
                $this->deleteDir($dir, $user);
                return ['status' => 'success', 'message' => '操作成功'];
            } else {
                return ['status' => 'fail', 'message' => '操作失败'];
            }
        }
    }

    /**
     * 纯文件文件下载
     */
    public function singleFileUpload($fileIds, $type, $company_id)
    {
        $user = auth('api')->user();
        $user_id = FunctionTool::encrypt_id($user->id);
        $company_id = $company_id === null ? null : FunctionTool::decrypt_id($company_id);
        //通过文件id获取文件
        if ($type == 'personal') {
            $files = DB::table('personal_oss_file')->whereIn('id', $fileIds)->get()->toarray();
        } else {
            $files = DB::table('oss_file')->whereIn('id', $fileIds)->get()->toarray();
        }

        //文件最近使用情况记录
        $data = [];
        foreach ($files as $file) {
            $data[] = [
                'name' => $file->name,
                'type' => '下载',
                'path' => $file->oss_path,
                'user_id' => $user->id,
                'company_id' => $company_id,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }
        self::recordUseFile($data);
        if (count($files) == 1) {//单个文件下载
            // 指定文件下载路径。
            $localfile = $user_id . $files[0]->name;
            $options = array(
                OssClient::OSS_FILE_DOWNLOAD => $localfile
            );
            //获取文件类型
            $explode = explode('.', $files[0]->oss_path);
            $type = $explode[count($explode) - 1];

            $ossClient = new OssClient(env('Aliyun_OSS_AccessKeyId'), env('Aliyun_OSS_AccessKeySecret'), env('Aliyun_OSS_endpoint'));
            $ossClient->getObject('gzts', $files[0]->oss_path, $options);
            //将文件返回客户端下载
            header("Content-Type: application/zip");
            header("Content-Transfer-Encoding: Binary");
            header("Content-Length: " . filesize($localfile));
            header("filename:" . urlencode($files[0]->name));
            readfile($localfile);
            //删除本地文件
            unlink($localfile);
            exit();
        } elseif (count($files) > 1) {//多个文件打包下载
            $unlink = [];
            foreach ($files as $file) {
                // 指定文件下载路径。
                $localfile = $user_id . $file->name;
                $unlink[] = $localfile;
                $options = array(
                    OssClient::OSS_FILE_DOWNLOAD => $localfile
                );
                $ossClient = new OssClient(env('Aliyun_OSS_AccessKeyId'), env('Aliyun_OSS_AccessKeySecret'), env('Aliyun_OSS_endpoint'));
                $ossClient->getObject('gzts', $file->oss_path, $options);
            }
            $unlink = array_unique($unlink);
            //压缩文件
            Zipper::make($user_id . '.zip')->add($unlink)->close();
            //循环删除项目本地word文件
            foreach ($unlink as $file) {
                unlink($file);
            }
            header("Content-Type: application/zip");
            header("Content-Transfer-Encoding: Binary");
            header("Content-Length: " . filesize($user_id . '.zip'));
            header("filename:" . $user_id . ".zip");
            readfile($user_id . '.zip');
            unlink($user_id . '.zip');
            exit();
        }
    }

    /**
     * 打包下载
     */
    public function downloadPackage($data)
    {
        $company_id = array_get($data, 'company_id') === null ? null : FunctionTool::decrypt_id($data['company_id']);
        $fileIds = $data['fileIds'] === null ? [] : FunctionTool::decrypt_id_array($data['fileIds']);
        //通过文件id获取文件
        //通过文件id获取文件
        if ($data['type'] == 'personal') {
            $singleFiles = DB::table('personal_oss_file')->whereIn('id', $fileIds)->pluck('oss_path')->toarray();
        } else {
            $singleFiles = DB::table('oss_file')->whereIn('id', $fileIds)->pluck('oss_path')->toarray();
        }

        $dirs = $data['dirs'];
        $user = auth('api')->user();
        $user_id = FunctionTool::encrypt_id($user->id);
        //压缩文件
        $zipper = new \Chumper\Zipper\Zipper();
        $zipper->make($user_id . '.zip');
        $use_file_path = $singleFiles;
        $unlink = [];
        $ossClient = new OssClient(env('Aliyun_OSS_AccessKeyId'), env('Aliyun_OSS_AccessKeySecret'), env('Aliyun_OSS_endpoint'));
        //非目录下的文件
        if (count($singleFiles) > 0) {
            foreach ($singleFiles as $oss_path) {
                //获取文件名称
                $explode = explode('/', $oss_path);
                $file_name = $explode[count($explode) - 1];
                //获取文件类型
                $explode = explode('.', $oss_path);
                $type = $explode[count($explode) - 1];
                // 指定文件下载路径。
                $localfile = $user_id . $file_name;
                $unlink[] = $localfile;
                $options = array(
                    OssClient::OSS_FILE_DOWNLOAD => $localfile
                );
                $ossClient->getObject('gzts', $oss_path, $options);
                //循环添加文件到zip
                $zipper->add($localfile);
            }
        }

        //目录下的文件
        foreach ($dirs as $ml) {
            $explodeDir = explode('/', $ml);
            $explodeDir = $explodeDir[count($explodeDir) - 2];
            if ($data['type'] == 'personal') {//个人目录
                $dir = User::find($user->id)->oss->root_path . $ml;
            } else {//公司目录
                $dir = Company::find($user->current_company_id)->oss->root_path . 'public/' . $ml;
            }
            $files = Storage::disk('oss')->allFiles($dir);
            foreach ($files as $oss_path) {
                $use_file_path[] = $oss_path;
                //获取文件名称
                $explode = explode('/', $oss_path);
                $file_name = $explode[count($explode) - 1];
                //获取文件类型
                $explode = explode('.', $oss_path);
                $type = $explode[count($explode) - 1];
                // 指定文件下载路径。
                $localfile = $user_id . $file_name;
                $unlink[] = $localfile;
                $options = array(
                    OssClient::OSS_FILE_DOWNLOAD => $localfile
                );
                $ossClient->getObject('gzts', $oss_path, $options);
                //循环添加文件到zip
                $zipper->folder($explodeDir)->add($localfile);
            }
        }
        $zipper->close();
        $unlink = array_unique($unlink);
        //循环删除项目本地word文件
        foreach ($unlink as $file) {
            unlink($file);
        }
        //文件最近使用情况记录
        $use_record = [];
        foreach ($use_file_path as $path) {
            $use_record[] = [
                'type' => '下载',
                'path' => $path,
                'user_id' => $user->id,
                'company_id' => $company_id,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }
        self::recordUseFile($data);
        //文件返回给浏览器
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length: " . filesize($user_id . '.zip'));
        header("filename:" . $user_id . ".zip");
        readfile($user_id . '.zip');
        unlink($user_id . '.zip');
        exit();
    }

    /**
     * 文件动态
     */
    public function fileDynamics()
    {
        //所有公司文件
//        $company=OssRecord::where('company_id',$user->current_company_id)->get()->map(function ($item){
//            return [
//                'type'=>$item->type,
//                'size'=>$item->size,
//                'file_name'=>$item->file_name,
//                'dir'=>$item->dir,
//                'company_name'=>$item->company==null?'':$item->company->name,
//                'user_name'=>$item->user==null?'':$item->user->name,
//            ];
//        });
        //个人的文件(个人上传的包含上传到个人磁盘和公司磁盘的所有文件)
        $user = auth('api')->user();
        return OssRecord::where('user_id', $user->id)->get()->map(function ($item) {
            return [
                'type' => $item->type,
                'size' => $item->size,
                'file_name' => $item->file_name,
                'dir' => $item->dir,
                'company_name' => $item->company_id == null ? '' : $item->company->name,
                'user_name' => $item->user_id == null ? '' : $item->user->name,
            ];
        });
    }

    /**
     * 记录最近使用
     */
    public static function recordUseFile($data)
    {
        DB::table('file_use_record')->insert($data);
    }

    /**
     * 最近使用
     */
    public function recentlyUsed()
    {
        //文件使用情况
        $user = auth('api')->user();
        return FileUseRecord::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(20, 0)
            ->get()
            ->map(function ($item) {
                $file = $item->company_id === null ? $item->user_file_id : $item->company_file_id;
                return [
                    'user_id' => FunctionTool::encrypt_id($item->user_id),
                    'company_id' => $item->company_id === null ? null : FunctionTool::encrypt_id($item->company_id),
                    'type' => $item->company_id === null ? 'personal' : 'company',
                    'action_type' => $item->type,
                    'name' => $item->name,
                    'path' => $item->path,
                    'file_id' => $file === null ? null : FunctionTool::encrypt_id($file->id),
                    'created_at' => Carbon::parse($item->created_at)->toDateTimeString(),
                ];
            });
    }

    /**
     * 批量删除
     */
    public function batchDelete($dirs, $fileIds)
    {
        $fileIds = count($fileIds) === 0 ? [] : FunctionTool::decrypt_id_array($fileIds);
        $user = auth('api')->user();
        //删除目录
        foreach ($dirs as $dir) {
            $this->deleteDir($dir, $user);
        }
        //删除文件
        foreach ($fileIds as $fileId) {
            self::deleteFile($fileId);
        }
        return ['status' => 'success', 'message' => '删除成功'];
    }

    /**
     * 批量复制
     */
    public function batchCopy($dirs, $fileIds, $type, $target_directory, $company_id)
    {
        //目录复制
        $this->copyFolder(['dirs' => $dirs, 'type' => $type, 'target_directory' => $target_directory, 'company_id' => $company_id,]);
        //文件复制
        if ($type === 'personal') {//复制到个人磁盘下
            foreach ($fileIds as $file_id) {
                $jg = self::copyFileToPath($file_id, $target_directory);
            }
        } else {//复制到公司磁盘下
            foreach ($fileIds as $file_id) {
                $jg = self::copyFileToCompany($file_id, $target_directory, $company_id);
            }
        }
        return ['status' => 'success', 'message' => '复制成功'];
    }

    /**
     * 批量移动
     */
    public function batchMove($file_ids, $target_directory, $dirs, $type, $company_id)
    {
        //移动文件
        foreach ($file_ids as $file_id) {
            $this->moveFile(['file_id' => $file_id, 'target_directory' => $target_directory]);
        }
        //移动目录
        $this->moveFolder(['dirs' => $dirs, 'target_directory' => $target_directory, 'type' => $type, 'company_id' => $company_id]);
        return ['status' => 'success', 'message' => '移动成功'];
    }

    /**
     * @throws \OSS\Core\OssException
     * 实例化ali用户磁盘
     */
    public function link()
    {
//        dd(OssRecord::find(2)->user);
//        $a=Storage::disk('oss')->allDirectories();
//        dd($a);
        $ossClient = new OssClient(env('Aliyun_OSS_AccessKeyId'), env('Aliyun_OSS_AccessKeySecret'), env('Aliyun_OSS_endpoint'));
//        $a=$ossClient->getObject('gzts','oss');
//        dd($a);
        $Metas = $ossClient->listObjects('gzts', ['prefix' => 'company/company1/']);
//        $Metas=$ossClient->copyObject('gzts','user/user14/111','gzts','user/user14/111/123/111');
//        $Metas=$ossClient->getObjectMeta('gzts','user/user1');
        dd($Metas);
//        $ossClient->copyObject('gzts','user/user14/toxiang/','gzts','user/user14/mmm/');//文件拷贝
    }
}