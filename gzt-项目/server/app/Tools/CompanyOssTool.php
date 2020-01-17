<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;

use App\Http\Resources\FileResource;
use App\Interfaces\CompanyOssInterface;
use App\Interfaces\UserOssInterface;
use App\Models\Company;
use App\Models\CompanyOss;
use App\Models\OssFile;
use App\Models\PersonalOssFile;
use App\Models\User;
use App\Models\UserOss;
use App\Repositories\CompanyOssRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\OssFileRepository;
use App\Repositories\UserOssRepository;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * 企业云存储工具类
 */
class CompanyOssTool implements CompanyOssInterface
{
    static private $companyOssTool;
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
    static public function getCompanyOssTool()
    {
        if (self::$companyOssTool instanceof self) {
            return self::$companyOssTool;
        } else {
            return self::$companyOssTool = new self;
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
    public function makeDir($path, Company $company)
    {
        //是否有网盘管理权限的判断
        if (!true) {
            json_encode(['status' => 'fail', 'message' => '没有操作权限']);
        }
        if (Storage::makeDirectory($company->oss->root_path . 'public' . $path)) {
            return json_encode(['status' => 'success', 'message' => '目录创建成功']);
        } else {
            return json_encode(['status' => 'fail', 'message' => '目录创建不成功']);
        };
    }

    /**
     * 删除一个目录
     * @param $path
     */
    public function deleteDir($path, Company $company)
    {
        //是否有网盘管理权限的判断
        if (!true) {
            return json_encode(['status' => 'fail', 'message' => '没有操作权限']);
        }
        //更新磁盘大小
        $size = $this->getSizeByDir($company->oss->root_path . 'public' . $path);
        self::updateNowSize($company->id, $size, 'sub');
        //查询当前目录下是否有文件
        if (OssFileRepository::directoryHasFile($company->oss->root_path . 'public' . $path)) {
            return json_encode(['status' => 'fail', 'message' => '当前目录下有文件不能删除']);
        }
        //删除目录
        if (Storage::deleteDirectory($company->oss->root_path . 'public' . $path)) {
//            $this->getNowSize($company);
            return json_encode(['status' => 'success', 'message' => '目录删除成功']);
        } else {
            return json_encode(['status' => 'fail', 'message' => '目录删除不成功']);
        };
    }

    /**
     * 获取企业云指定目录的信息--目录&文件
     */
    public function getTargetDirectoryInfo(Request $request)
    {
        //返回的信息格式
        $data = ['directories' => [], 'files' => [],];
        $publicDir = $request->get('publicDir', 'public/');

        try {
            $company_id = FunctionTool::decrypt_id($request->company_id);
        } catch (\Exception $e) {
            return json_encode(['status' => 'fail', 'message' => '没有找到该公司的相关信息']);
        }
        $company = Company::find($company_id);
        if ($company == null) {
            return json_encode(['status' => 'fail', 'message' => '没有找到该公司的相关信息']);
        }

        $target_directory = $company->oss->root_path . $publicDir . $request->target_directory;
        $directories = Storage::disk('oss')->directories($target_directory);
        $dirs = [];
        $pattern = '/^' . preg_replace('/"/', '', json_encode($target_directory)) . '([\s\S]*)$/';
        foreach ($directories as $v) {
            preg_match($pattern, $v, $matches);
//            $allSize=PersonalOssTool::getSizeByDir($v);
            $dirs[] = ['name' => $matches[1], 'size' => null, 'dir' => $v, 'type' => 'folder'];
        }
        $data['directories'] = $dirs;//获取目录信息
        $data['files'] = FileResource::collection(OssFileRepository::getFilesByDirectory($target_directory))->toArray(1);
        return json_encode(['status' => 'success', 'data' => $data]);
    }

    /**
     * 拿到当前存储总容量
     * @param $rootPath
     */
    public function getNowSize(Company $company): int
    {
        $files = Storage::allFiles($company->oss->root_path);
        $size = 0;
        foreach ($files as $file) {
            $size += Storage::size($file);
        }
        CompanyOssRepository::updateOss($company->oss, ['now_size' => $size]);//更新company_oss存储信息
        return $size;
    }

    /**
     * 生成企业云存储根目录(在企业创建的时候调用)
     * @param $id
     */
    public function makeRootPath(Company $company)
    {
        $rootPath = config('oss.company.path') . 'company' . ($company->id) . '/';
        if (Storage::makeDirectory($rootPath)) {
            CompanyOss::create([
                'company_id' => $company->id,
                'root_path' => $rootPath,
                'name' => '企业网盘',
                'all_size' => config('oss.company.default_size') * 1024 * 1024,//单位/kb
                'expire_date' => time() + 30000000,
            ]);
            return json_encode(['status' => 'success', 'message' => '目录创建成功']);
        } else {
            return json_encode(['status' => 'fail', 'message' => '目录创建不成功']);
        };
    }

    /**
     * 获取指定目录下的文件大小
     * @param $path
     */
    public function getDirSize($path, Company $company)
    {
        $files = Storage::allFiles($company->oss->root_path . $path);
        $size = 0;
        foreach ($files as $file) {
            $size += Storage::size($file);
        }
        $this->companyOssRepository->updateOss($company->oss, ['now_size' => $size]);//更新company_oss存储信息
        return $size;
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
     * 更换网盘名称(暂定)
     * @param $name
     */
    public function alterName($name)
    {
        // TODO: Implement alterName() method.
    }

    /**
     * 添加指定文件的浏览记录
     * request 中需要file_id--文件id,type--文件操作类型
     * 返回
     */
    public function addFileBrowseRecord(Request $request)
    {
        //组装插入数据
        $data = [];
        DB::table('oss_file_browse_record')
            ->insert($data);
        return 0;
    }

    /**
     * 获取指定文件的浏览记录
     * request 中需要file_id--目标文件id,now_page--当前页数,page_size--每页大小
     * 返回
     */
    public function getFileBrowseRecord(Request $request)
    {
        $page_size = $request->get('page_size', 10);
        $offset = ($request->now_size - 1) * $page_size;
        $file_id = FunctionTool::decrypt_id($request->file_id);
        //分页查询记录
        $data = OssFileRepository::getFileBrowseRecord($offset, $page_size, $file_id);
        return json_encode([
            'status' => 'success',
            'count' => 0,//数据总数
            'data' => [],//浏览记录数据
        ]);
    }

    /**
     * 更新文件名
     * request所需参数:name-文件名(包含扩展名),directory-当前目录名,file_id--文件id
     */
    public function updateFileName(Request $request)
    {
        $name = $request->name;
        $directory = $request->directory;
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
        if (OssFileRepository::directoryExsitFile($name, 'public' . $directory)) {
            return json_encode(['status' => 'fail', 'message' => '当前目录有重名文件']);
        }
        //数据更改
        OssFileRepository::updateFile(FunctionTool::decrypt_id($request->file_id), $directory, $name);
        return json_encode(['status' => 'success', 'message' => '文件更新成功!']);
    }

    /**
     * 企业文件上传
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
    public static function strogeOssFile(array $file_data, $data, &$form_template = null, $k = null, $fk = null)
    {
        $filename = $file_data['name'];//取出文件名
        $realpath = $file_data['real_path'];//取出文件临时路径
        $size = round($file_data['size'] / 1024, 2);//文件的大小/kb
        $origin_name = $file_data['name'];//原始文件全名
        $extenName = FunctionTool::get_file_extension_name($filename);//文件扩展名
        $filename = FunctionTool::encrypt_id($data['model_id']) . str_random(8) . '.' . $extenName;//存放的文件名
        //验证云盘空间大小
        $message = self::ossSizeIsEnough($data['company_id'], [0 => ['name' => $filename, 'size' => $size]]);
        if (count($message) != 0) {
            return json_encode(['status' => 'fail', 'message' => implode(',', $message)]);
        }
        DB::beginTransaction();
        try {
            Storage::disk('oss')->put($data['oss_path'] . '/' . $filename,
                file_get_contents($realpath));
            //企业云盘空间累加空间--更新企业云存储空间
            //写入操作记录
            $user = auth('api')->user();
            $dir = str_replace(auth('api')->user()->oss->root_path, '', $data['oss_path']);
            $record = [
                'company_id' => $user->current_company_id,
                'user_id' => $user->id,
                'content' => '',
                'type' => '上传文件',
                'size' => $size,
                'file_name' => $origin_name,
                'dir' => $dir,
                'created_at' => date('Y-m-d H:i:s', time()),
            ];
            DB::table('company_oss_record')->insert($record);
            self::updateNowSize($data['company_id'], $size, 'add');
            //文件oss路径映射关系
            $file = OssFile::create(['name' => $origin_name,
                'company_id' => $data['company_id'],
                'uploader_id' => $data['uploader_id'],
                'size' => $size,
                'oss_path' => $data['oss_path'] . '/' . $filename,
            ]);
            //文件与model多态关系--$data['model_id']==null时表示不进行多态文件关联
            if ($data['model_id'] != null) {
                DB::table('model_has_file')->insert(['model_id' => $data['model_id'], 'model_type' => $data['model_type'], 'file_id' => $file->id]);
            }
            //$return原数据,$k文件在表单中对应的健
            if ($form_template != null) {
                $form_template[$k]['value'][$fk] = [
                    'id' => FunctionTool::encrypt_id($file->id),
                    'name' => $file->name,//文件名
                    'oss_path' => config('oss.root_path') . $file->oss_path,//云路径
                ];
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
     * 操作企业当前存储空间--企业云盘空间大小的操作(只改变)
     * @param $company_id :企业id
     * @param $size :文件大小/kb
     * @param $type :add--表示累加,--sub表示减
     */
    public static function updateNowSize($company_id, $size, $type)
    {
        $record = CompanyOssRepository::getRecord($company_id);
        CompanyOssRepository::updateOss($record, ['now_size' => $type == 'add' ? $record->now_size + $size : $record->now_size - $size
//                                                ,'all_size'=>$type=='add'?$record->all_size-$size:$record->all_size+$size
        ]);
    }

    /**
     * 计算企业云存储的剩余空间是否满足附件的大小
     */
    public static function ossSizeIsEnough(int $company_id, array $files)
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
        $c_oss = Company::find($company_id)->oss;//获取企业对应的oss实例
        if ($c_oss->now_size + $all_size > $c_oss->all_size) {
            $error_message[] = '云盘空间不足';
        }
        return $error_message;
    }

    /**
     * 文件复制到指定目录(企业内部复制)
     */
    public static function copyFileToPath($file_id, $target_directory, $company_id)
    {
        $company_id = FunctionTool::decrypt_id($company_id);
        $user_id = auth('api')->id();
        //验证是否有文件管理权
        if (!true) {
            return ['您没有文件管理权'];
        }
        //获取目标文件
        $file = OssFile::find($file_id);
        //判断空间大小是否支持文件复制(需要将单个file变为数组)并接收返回信息
        $message = self::ossSizeIsEnough($company_id, [0 => ['name' => $file->name, 'size' => $file->size]]);
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
        OssFile::create([
            'uploader_id' => $user_id,
            'company_id' => $company_id,
            'size' => $file->size,
            'name' => $file->name,
            'oss_path' => $to_path,//新文件路径
        ]);
        //更新企业云oss空间

        self::updateNowSize($company_id, $file->size, 'add');
        return json_encode(['status' => 'success', 'message' => '文件转存成功']);
    }

    /**
     * 文件复制到指定目录(企业文件复制到个人)
     */
    public static function copyFileToPersonal($file_id, $target_directory)
    {
        $user = auth('api')->user();
        //验证是否有文件管理权
        if (!true) {
            return ['您没有文件管理权'];
        }
        //获取目标文件
        $file = OssFile::find($file_id);
        //判断空间大小是否支持文件复制(需要将单个file变为数组)并接收返回信息
        $message = PersonalOssTool::ossSizeIsEnough($user->id, [0 => ['name' => $file->name, 'size' => $file->size]]);
        if (count($message) != 0) {
            return json_encode(['status' => 'fail', 'message' => implode(',', $message)]);
        }
        //截取文件的扩展名--在$matches[1]中
        preg_match(config('regex.file_extension'), $file->name, $matches);
        //复制文件至指定路径
        $to_path = User::find($user->id)->oss->root_path . $target_directory .
            FunctionTool::encrypt_id($file->id) . str_random(8) . '.' . $matches[1];
        Storage::disk('oss')->copy($file->oss_path, $to_path);
        //创建新的文件
        PersonalOssFile::create([
            'uploader_id' => $user->id,
            'user_id' => $user->id,
            'size' => $file->size,
            'name' => $file->name,
            'oss_path' => $to_path,//新文件路径
        ]);
        //更新企业云oss空间
        PersonalOssTool::updateNowSize($user->id, $file->size, 'add');
        return json_encode(['status' => 'success', 'message' => '文件转存成功']);
    }

    /**
     * 复制文件夹
     */
    public function copyFolder($data)
    {
        $type = $data['type'];
        $dirs = $data['dirs'];
        $target_dir = $data['target_directory'];
        $from_company_id = FunctionTool::decrypt_id($data['from_company_id']);
        //目录下的文件
        $user = auth('api')->user();
        foreach ($dirs as $ml) {
            $dir = Company::find($from_company_id)->oss->root_path . 'public/' . $ml;
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
                $file_id = OssFile::where('oss_path', $oss_path)->value('id');
                //如果为找到对应文件,则跳出本次循环
                if ($file_id === null) {
                    continue;
                }
                if ($type === 'company') {//复制到公司磁盘下
                    $jg = self::copyFileToPath($file_id, $target_directory, $data['company_id']);
                } else {//复制到个人磁盘下
                    $jg = self::copyFileToPersonal($file_id, $target_directory);
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
        $file = OssFile::find($file_id);
        //删除阿里云文件
        $state = Storage::disk('oss')->delete($file->oss_path);
        if ($state) {
            //删除文件
            $file->delete();
            //写入操作记录
            $user = auth('api')->user();
            $record = [
                'company_id' => $user->current_company_id,
                'user_id' => $user->id,
                'content' => '',
                'type' => '删除文件',
                'size' => $file->size,
                'file_name' => $file->name,
                'dir' => null,
                'created_at' => date('Y-m-d H:i:s', time()),
            ];
            DB::table('company_oss_record')->insert($record);
            //删除文件关联
            DB::table('model_has_file')
                ->where('file_id', $file_id)
                ->delete();
            //更新企业云存储空间
            self::updateNowSize($file->company_id, $file->size, 'sub');
            return json_encode(['status' => 'success', 'message' => '文件删除成功']);
        } else {
            return json_encode(['status' => 'fail', 'message' => '文件删除失败']);
        }
    }

    /**
     * 转移文件
     */
    public function moveFile($data)
    {
        $file_id = $data['file_id'];
        $newPath = $data['target_directory'] == '/' ? '/' : '/' . $data['target_directory'];
        $company_id = FunctionTool::decrypt_id($data['company_id']);
        try {
            DB::beginTransaction();
            $file_id = FunctionTool::decrypt_id($file_id);
            //验证是否有文件管理权
            if (!true) {
                return ['您没有文件管理权'];
            }
            //获取目标文件
            $file = OssFile::find($file_id);
            //判断空间大小是否支持文件复制(需要将单个file变为数组)并接收返回信息
//            $message = self::ossSizeIsEnough($company_id, [0 => ['name' => $file->name, 'size' => $file->size]]);
//            if (count($message) != 0) {
//                return json_encode(['status' => 'fail', 'message' => implode(',', $message)]);
//            }
            //截取文件的扩展名--在$matches[1]中
            preg_match(config('regex.file_extension'), $file->name, $matches);
            //复制文件至指定路径
            $to_path = Company::find($company_id)->oss->root_path . 'public' . $newPath .
                FunctionTool::encrypt_id($file->id) . str_random(8) . '.' . $matches[1];
            Storage::disk('oss')->move($file->oss_path, $to_path);
            //创建新的文件
            OssFile::where('id', $file_id)
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
     * 转移文件夹
     */
    public function moveFolder($data)
    {
        $company = Company::find(FunctionTool::decrypt_id($data['from_company_id']));
        $dirs = $data['dirs'];
        foreach ($dirs as $dir) {
            $status = $this->copyFolder($data)['status'];
            if ($status === 'success') {
                //删除原目录
                $this->deleteDir('/' . $dir, $company);
                return ['status' => 'success', 'message' => '操作成功'];
            } else {
                return ['status' => 'fail', 'message' => '操作失败'];
            }
        }
    }

    /**
     * 批量删除
     */
    public function batchDelete($dirs, $fileIds, $company_id)
    {
        $fileIds = count($fileIds) === 0 ? [] : FunctionTool::decrypt_id_array($fileIds);
        $company = Company::find(FunctionTool::decrypt_id($company_id));
        //删除目录
        foreach ($dirs as $dir) {
            $this->deleteDir('/' . $dir, $company);
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
    public function batchCopy($dirs, $fileIds, $type, $target_directory, $company_id, $from_company_id)
    {
        //目录复制
        $this->copyFolder(['dirs' => $dirs, 'type' => $type, 'target_directory' => $target_directory, 'from_company_id' => $from_company_id, 'company_id' => $company_id,]);
        //文件复制
        $fileIds = FunctionTool::decrypt_id_array($fileIds);
        if ($type === 'company') {//
            foreach ($fileIds as $file_id) {
                $jg = self::copyFileToPath($file_id, $target_directory, $company_id);
            }
        } else {//
            foreach ($fileIds as $file_id) {
                $jg = self::copyFileToPersonal($file_id, $target_directory);
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
     * @param $array_files ,为关联数组[$k=>[file1,2...],]
     * @param array $data
     * @param $form_template
     * @return array
     * 仅限拖拽表单中文件上传
     * 针对与拖拽表单,表单中拖入多个附件上传框及每个上传框上传多个文件
     */
    public static function uploadFormfile($array_files, array $data, &$form_template)
    {
        /**
         * 验证是否有企业文件上传的权限
         */
        if (!true) {
            return ['您没有上传文件的权限'];
        }
        //获取所有的文件
//        $files=$request->get(config('filesystems.upload.up_files_name'));
        if (count($array_files) == 0) {
            return ['status' => 'fail', 'message' => '没有可上传的文件'];
        }
        $error_message = [];//文件上传错误信息
        $regex = config('filesystems.upload.allow_file_type');//能够上传的文件格式
        $name = config('filesystems.upload.file.name');//文件name属性
        $size = config('filesystems.upload.file.size');//文件size属性
        $real_path = config('filesystems.upload.file.real_path');//文件real_path属性
        $max_size = config('filesystems.upload.max_size');//单文件上传阈值
        //开始上传文件
        foreach ($array_files as $k => $files) {
            foreach ($files as $fk => $file) {
//                $form_template[$k]['value'];
                //文件格式大小合法性校验
                if (preg_match($regex, $file['name']) == 0) {
                    $error_message[] = $file[$name] . ':格式不合法';
                    continue;
                } elseif ($file[$size] > $max_size * 1024 * 1024) {
                    $error_message[] = $file[$name] . ':大小超出' . $max_size . 'M';
                    continue;
                }
                //进行文件存储&添加文件关联
                $state = self::strogeOssFile(['name' => $file['name'], 'real_path' => $file['thumbUrl'], 'size' => $file['size']], $data, $form_template, $k, $fk);
                if (!$state) {
                    $error_message[] = $file[$name] . ':上传出错';
                }
            }
        }
        return $error_message;
    }
}