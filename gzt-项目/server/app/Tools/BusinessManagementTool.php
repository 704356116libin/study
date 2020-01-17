<?php

namespace App\Tools;

use App\Http\Resources\company\CompanyResource;
use App\Interfaces\BusinessManagementInterface;
use App\Models\Company;
use App\Models\CompanyBasisLimit;
use App\Models\CompanyExternalContact;
use App\Models\CompanyHasFun;
use App\Models\CompanyLicense;
use App\Models\CompanyLogo;
use App\Models\CompanyOperationLog;
use App\Models\CompanyOss;
use App\Models\CompanyPartner;
use App\Models\CompanyPartnerRecord;
use App\Models\CompanyPartnerSort;
use App\Models\CompanyVersion;
use App\Models\Department;
use App\Models\ExternalCompanyGroup;
use App\Models\ExternalContactType;
use App\Models\ExternalGroupRelate;
use App\Models\PerSort;
use App\Models\Role;
use App\Models\User;
use App\Models\UserCompany;
use App\Models\UserCompanyInfo;
use App\Repositories\CompanyDepartmentRepository;
use Carbon\Carbon;
use Clarkeash\Doorman\Facades\Doorman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Storage;

class BusinessManagementTool implements BusinessManagementInterface
{
    public static $businessManagementTool;
    private $getValidateTool;
    private $userOssTool;
    private $userRepository;
    private $departmentTool;
    private $departmentRepository;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->getValidateTool = ValidateTool::getValidateTool();
        $this->userOssTool = UserOssTool::getUserOssTool();
        $this->userRepository = UserRepository::getUserRepository();
        $this->departmentTool = DepartmentTool::getDepartmentTool();
        $this->departmentRepository=CompanyDepartmentRepository::getDepartmentRepository();
    }

    /**
     * 单例模式,实例化自身类并返回
     */
    public static function getBusinessManagementTool()
    {
        if (self::$businessManagementTool instanceof self) {
            return self::$businessManagementTool;
        } else {
            return self::$businessManagementTool = new self();
        }
    }

    /**
     * 防止克隆
     */
    private function _clone()
    {
    }

    /**
     * 企业后台首页
     */
    public function index()
    {
        $company_id=auth('api')->user()->current_company_id;
        $company=$this->companyData()['data'];
        $companyBasisLimit=CompanyBasisLimit::where('company_id',$company_id)->get();
        $companyBasisLimit=$companyBasisLimit->mapWithKeys(function ($item) use ($company_id){
            return [
                $item->type => [
                    'type'=>array_get(config('companybasiclimit.company'),$item->type),
                    'type_number'=>$item->type_number,
                    'expire_date'=>date('Y-m-d H:i:s',$item->expire_date),
                    'use_number'=>$this->useNumber($item->type,$item->use_number,$company_id),
                ]
            ];
        });
        $oss=CompanyOss::where('company_id',$company_id)->first();
        $companyBasisLimit['disk']=[
            'type'=>'企业磁盘',
            'type_number'=>$oss->all_size,
            'expire_date'=>null,
            'use_number'=>$oss->now_size,
        ];
        return [
            'base_info' => $company,
            'base_limit' => $companyBasisLimit,
        ];
    }
    /**
     * 获取公司信息
     */
    public function companyData()
    {
        $user = auth('api')->user();
        $company=Company::where('id',$user->current_company_id)->first();
        $data=[];
        if($company){
            $data=[
                'logo'=>array_get($this->company_logo($user->current_company_id),'data'),//公司logo
                'name'=>$company->name,               //公司名称
                'verified'=>$company->verified,       //公司认证标识,0未认证,1等待认证,2审核通过,3审核不通过
                'abbreviation'=>$company->abbreviation,//公司简称
                'number'=>$company->number,           //企业号
//                'logo_id'=>logo_id,         //公司logo 的id
                'tel'=>$company->tel,                 //企业电话
                'type'=>$company->type,               //企业类型
                'district'=>json_decode($company->district),       //所属地区json
                'industry'=>$company->industry,       //所属行业json
                'address'=>$company->address,         //公司地址
                'zip_code'=>$company->zip_code,       //邮编
                'fax'=>$company->fax,                 //传真
                'url'=>$company->url,                 //公司网址
            ];
        }
        return ['status'=>'success','data'=>$data];
    }
    /**
     * 保存企业信息
     */
    public function enterpriseInfoSave($data)
    {
        try {
            //敏感词过滤
            $this->getValidateTool->sensitive_word_validate(['name' => $data['name']]);

            $logo=array_get($data,'upload');//公司logo
            $logo[0]['tmp_name']=$logo[0]['thumbUrl'];
            if($logo!=null){
                /**
                 * 清除原logo
                 */
                $company_logo=$this->company_logo();
                if($company_logo['status']=='success'){
                    CompanyOssTool::deleteFile($company_logo['data']['id']);
                }
                //上传logo
                $logo=$this->uploadLogo($logo);
                if(!$logo){
                    return ['status'=>'fail','message'=>'logo上传失败'];
                }
            }
            $user = auth('api')->user();
            $company_data=[
                'name'=>array_get($data,'name'),               //公司名称
//                'creator_id'=>creator_id,   //公司创建者的id
//                'verified'=>verified,       //公司认证标识,0未认证,1等待认证,2审核通过,3审核不通过
//                'email_count'=>email_count, //可用邮件条数
//                'sms_count'=>sms_count,     //可用短信条数
                'abbreviation'=>array_get($data,'abbreviation'),//公司简称
                'number'=>array_get($data,'number'),           //企业号
//                'logo_id'=>logo_id,         //公司logo 的id
                'tel'=>array_get($data,'tel'),                 //企业电话
                'type'=>array_get($data,'type'),               //企业类型
                'district'=>array_get($data,'area')==null?array_get($data,'area'):json_encode(array_get($data,'area')),       //所属地区json
                'industry'=>array_get($data,'industry'),       //所属行业string
                'address'=>array_get($data,'address'),         //公司地址
                'zip_code'=>array_get($data,'zip_code'),       //邮编
                'fax'=>array_get($data,'fax'),                 //传真
                'url'=>array_get($data,'internetSite'),                 //公司网址
//                'license_id'=>license_id,   //执照文件id
            ];
            //保存认证数据
            $count=Company::where('id',$user->current_company_id)->update($company_data);
//            $this->getEnterpriseCertificationRepository->createCertification($data);
            if($count>0){
                return ['status' => 'success', 'message' => '企业信息保存成功'];
            }else{
                return ['status' => 'fail', 'message' => '保存失败,企业信息只有创建者可以修改'];
            }
        } catch (\Exception $e) {
            echo $e;
            return ['status' => 'fail', 'message' => '企业信息保存失败'];
        }
    }
    private function uploadLogo($array_files)
    {
        try{
            DB::beginTransaction();
            $user=auth('api')->user();
            $company = Company::find($user->current_company_id);

            //获取企业认证关联表的id
            $CompanyLogo=CompanyLogo::where('company_id',$user->current_company_id)->first();
            if(!$CompanyLogo){
                $companyLogo_id=DB::table('company_logo')->insertGetId(['company_id'=>$user->current_company_id]);
            }else{
                $companyLogo_id=$CompanyLogo->id;
            }
            //上传认证文件
            $form_template=null;
            $data = CompanyOssTool::uploadFile($array_files, [
                'oss_path' => $company->oss->root_path . 'company_logo',//文件存入的所在目录
                'model_id' => $companyLogo_id,//关联模型id
                'model_type' => CompanyLogo::class,//关联模型类名
                'company_id' => $user->current_company_id,//所属公司id
                'uploader_id' => $user->id,//上传者id
            ]);
            if($data){
                $company->update(['logo_id'=>$companyLogo_id]);
                DB::commit();
                return true;
            }else{
                DB::rollBack();
                return false;
            }
        }catch (\Exception $exception){
            DB::rollBack();
            return ['status'=>'fail','message'=>'服务器错误'];
        }

    }

    /**
     * 保存公司认证文件
     * @param $array_files
     */
    public function companyEnterpriseFile($array_files)
    {
        try{
            DB::beginTransaction();
            $user=auth('api')->user();
            $company = Company::find($user->current_company_id);

            if($company->status==2||$company->status==1){
                return ['status'=>'fail','message'=>'提交失败,企业已认证成功或正在审核中'];
            }

            //获取企业认证关联表的id
            $companyLicense=CompanyLicense::where('company_id',$user->current_company_id)->first();
            if(!$companyLicense){
                $companyLicense_id=DB::table('company_license')->insertGetId(['company_id'=>$user->current_company_id]);
            }else{
                $companyLicense_id=$companyLicense->id;
            }

            /**
             * 先清除原来的认证
             */
            $enterpriseFile=$this->enterpriseFile();
            if(array_get($enterpriseFile,'status')!='fail'){
                CompanyOssTool::deleteFile($enterpriseFile['data']['id']);
            }
            //上传认证文件
            $form_template=null;
            $data = CompanyOssTool::uploadFormfile($array_files, [
                'oss_path' => $company->oss->root_path . 'company_license',//文件存入的所在目录
                'model_id' => $companyLicense_id,//关联模型id
                'model_type' => CompanyLicense::class,//关联模型类名
                'company_id' => $user->current_company_id,//所属公司id
                'uploader_id' => $user->id,//上传者id
            ],$form_template);
            if(count($data)==0){
                $company->update(['verified'=>1,'license_id'=>$companyLicense_id]);
                DB::commit();
                return ['status'=>'success','message'=>'文件上传成功,等待验证'];
            }else{
                DB::rollBack();
                return ['status'=>'fail','message'=>'认证文件上传失败失败'];
            }
        }catch (\Exception $exception){
            DB::rollBack();
            dd($exception->getMessage());
            return ['status'=>'fail','message'=>'服务器错误!'];
        }
    }
    /**
     * 获取认证企业信息
     */
    public function getEnterpriseFile()
    {
        $user=auth('api')->user();
        $verified=Company::find($user->current_company_id)->verified;
        $enterpriseFile=[];
        if($verified!=0){
            $enterpriseFile=$this->enterpriseFile();
            $enterpriseFile=array_get($enterpriseFile,'data');//认证文件
        }
        $enterpriseFile['verified']=$verified;
        return ['status'=>'success','enterpriseFile'=>$enterpriseFile];
    }
    /**
     * 企业认证文件
     */
    public function enterpriseFile($company_id=null)
    {
        $user=auth('api')->user();
        $company_id=$company_id==null?$user->current_company_id:$company_id;
        $files= CompanyLicense::where('company_id',$company_id)->first();
        if($files!=null&&count($files->files)>0){
            $oss_path=$files->files[0]->oss_path;
            $real_path=Storage::url($oss_path);
            $data=[
                'id'=>$files->files[0]->id,
                'name'=>$files->files[0]->name,
                'url'=>$real_path
            ];
            return ['status'=>'success','data'=>$data];
        }else{
            return ['status'=>'fail','message'=>'未上传认证文件'];
        }
    }
    /**
     * 企业logo图片
     */
    public function company_logo($company_id=null)
    {
        $user=auth('api')->user();
        $company_id=$company_id==null?$user->current_company_id:$company_id;
        $files= CompanyLogo::where('company_id',$company_id)->first();
        if($files!=null&&count($files->files)>0){
            $oss_path=$files->files[0]->oss_path;
            $real_path=Storage::url($oss_path);
            $data=[
                'id'=>$files->files[0]->id,
                'name'=>$files->files[0]->name,
                'url'=>$real_path
            ];
            return ['status'=>'success','data'=>$data];
        }else{
            return ['status'=>'fail','message'=>'未上传认证文件'];
        }
    }
    /************职务权限块****************/
    /**
     * 一个公司所有的角色
     */
    public function allRoles($data)
    {
        $now_page = array_get($data, 'now_page');
        $page_size = array_get($data, 'page_size');
        $user = auth('api')->user();
        $role_data = [];
//        FunctionTool::del('roles');//彻底清除软删除得数据
        $roles = Company::find($user->current_company_id)->roles->sortBy('sort');
        foreach ($roles as $v){
            $role_id = FunctionTool::encrypt_id($v->id);
            $role_name = $v->name;//角色名
            $userids = DB::table('company_user_role')->where('company_id',$user->current_company_id)->where('role_id',$v->id)->pluck('user_id')->all();
            $user_names = UserCompanyInfo::where('company_id',$user->current_company_id)->whereIn('user_id',$userids)->pluck('name')->all();
            $user_counts = count($userids);
            $role_data[] = [
                'id' => $role_id,
                'name' => $role_name,
                'user_counts' => $user_counts,
                'user_names' => $user_names,
            ];
        }
//        foreach ($roles as $v) {
//            $users = $v->role_users;
//            $role_id = FunctionTool::encrypt_id($v->id);
//            $role_name = $v->name;//角色名
//            $user_names = $users->pluck('name');//用户names
//            $user_counts = count($users->pluck('name'));//用户numbers
//            $role_data[] = [
//                'id' => $role_id,
//                'name' => $role_name,
//                'user_counts' => $user_counts,
//                'user_names' => $user_names,
//            ];
//        }
        $count = count($role_data);
        $role_data = array_slice($role_data, ($now_page - 1) * $page_size, $page_size);
        return ['count' => $count, 'data' => $role_data];
    }

    /**
     * 搜索角色
     */
    public function searchRole($role_name)
    {
        $user = auth('api')->user();
        $role = Company::find($user->current_company_id)->roles->where('name', $role_name)->all();
        return $role;
    }

    /**
     * 创建职务/角色(公司)
     */
    public function addRole($data)
    {
        try {
            DB::beginTransaction();
            $user = auth('api')->user();
            $get_user_c_per = RoleAndPerTool::get_user_c_per($user->id, $user->current_company_id);
            //判断用户权限
            if (!in_array('c_super_manage_per', $get_user_c_per)) {
                return ['status' => 'fail', 'message' => '权限不足'];
            }
            $company = Company::find($user->current_company_id);
            $per_array = $data['per_array'];
            $name = $data['name'];
//            $description=$data['description'];
            $role_data = [
                'name' => $name,
                'description' => null,
                'guard_name' => 'gzt',
                'is_personal' => 0,
            ];
            $role = Role::create($role_data);//创建角色
            $role->syncPermissions($per_array);//赋予角色基础权限
            $company->assignRole($role);//赋予公司角色
            //为该职务添加人员
//            $this->giveRoleUsers(['role'=>$role,'role_id'=>$role->id,'user_ids'=>$data['user_ids']]);
            DB::commit();
            return ['status' => 'success', 'message' => '角色创建成功'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'fail', 'message' => '服务器错误!!!'];
        }
    }

    /**
     * 为职务/角色添加用户
     */
    public function giveRoleUsers($data)
    {
        try {
            DB::beginTransaction();
            $user = auth('api')->user();
            $role_id = FunctionTool::decrypt_id($data['role_id']);
            $user_ids = FunctionTool::decrypt_id_array($data['user_ids']);
            $insert_data = [];
            $role = array_get($data, 'role') === null ? Role::find($role_id) : array_get($data, 'role');
            foreach ($user_ids as $v) {
                $insert_data[] = [
                    'company_id' => $user->current_company_id,
                    'user_id' => $v,
                    'role_id' => $role->id
                ];
            }
            //先清除该角色的关系
            DB::table('company_user_role')->where('role_id', $role_id)->delete();
            //将角色添加给某用户
            DB::table('company_user_role')->insert($insert_data);
            DB::commit();
            return ['status' => 'success', 'message' => '添加成功'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'fail', 'message' => '服务器错误!!'];
        }

    }
    /**
     * 每个用户在对应公司拥有个人信息存储
     */
    public function userDataInCompany()
    {
        $user_info=json_encode([
            'a'=>'123',
            'b'=>'456',
        ]);
        $a=UserCompany::where('user_id',1)->where('company_id',1)->update(['is_enable'=>1]);
        dd($a);




    }

    /**
     * 编辑职务
     */
    public function editRole($role_id)
    {
        $role_id=FunctionTool::decrypt_id($role_id);
        $role = Role::find($role_id);
        $role_id = $role->permissions->pluck('id')->toarray();
        $c_per = $this->c_per();
        $b = [];
        foreach ($c_per as $v) {
            $a = [];
            foreach ($v['data'] as $p) {
                if (in_array($p['id'], $role_id)) {
                    $a[] = $p['id'];
                }
            }
            $b[$v['id']] = $a;
        }
        $data = [
            'id' => $role->id,
            'name' => $role->name,
            'company_id' => $role->company_id,
            'description' => $role->description,
            'guard_name' => $role->guard_name,
            'per' => $b,
//            'per'=>$role->permissions->map(function ($per){
//                return ['id'=>$per->id,'name'=>$per->name,'description'=>$per->description];
//            }),
        ];
        return $data;
    }

    /**
     * 保存编辑职务
     */
    public function saveEditRole($data)
    {
        try {
            DB::beginTransaction();
            $user = auth('api')->user();
            $get_user_c_per = RoleAndPerTool::get_user_c_per($user->id, $user->current_company_id);
            //判断用户权限
            if (!in_array('c_super_manage_per', $get_user_c_per)) {
                return ['status' => 'fail', 'message' => '权限不足'];
            }
            $per_array = $data['per_array'];
            $name = $data['name'];
            $role_data = [
                'name' => $name,
                'guard_name' => 'gzt',
                'is_personal' => 0,
            ];
            $role = Role::find($data['id']);
            $role->update($role_data);//创建角色
            $role->syncPermissions($per_array);//赋予角色基础权限(此函数会,撤销角色原来的权限,重新赋予新的权限)
            DB::commit();
            return ['status' => 'success', 'message' => '职务编辑成功'];
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return ['status' => 'fail', 'message' => '服务器错误!!!'];
        }
    }

    /**
     * 删除职务
     */
    public function deleteRole($role_id)
    {
        try {
            DB::beginTransaction();
            $user = auth('api')->user();
            $role_id=FunctionTool::decrypt_id($role_id);
            $user_count = DB::table('company_user_role')->where('role_id', $role_id)->count();
            if ($user_count > 0) {
                return ['status' => 'fail', 'message' => '职务正在使用,不能删除'];
            }
            //删除公司与职务的关系
            Company::find($user->current_company_id)->removeRole($role_id);
            //删除角色,角色与权限的关系
            DB::table('role_per')->where('role_id', $role_id)->delete();
            Role::where('id', $role_id)->delete();
            DB::commit();
            return ['status' => 'success', 'message' => '删除成功'];
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return ['status' => 'fail', 'message' => '服务器错误!!'];
        }
    }

    /**
     * 公司基础权限
     */
    public function c_per()
    {
        $per = PerSort::all()->map(function ($perSort) {
            return [
                'id' => $perSort->id, 'name' => $perSort->name, 'data' => $perSort->pers->toarray()
            ];
        });
        return $per;
//        $company_id=auth('api')->user()->current_company_id;
//        return array_values(Company::find($company_id)->permissions->map(function ($per){
//            return ['id'=>$per->id,'name'=>$per->name,'description'=>$per->description,'sort'=>$per->sort->name];
//        })->groupBy('sort')->toarray());
    }


    /************组织结构*******************/
    /**
     * 1生成邀请码
     */
    public function generateInvitationCode()
    {
        $user = auth('api')->user();
        //判断用户是否有生成验证码的权限

        //

        $generateInvitationCode = Doorman::generate()->uses(20)->expiresIn(1)->make();
        $data = [
            'invite_code' => $generateInvitationCode[0]->code,
            'company_code' => FunctionTool::encrypt_id($user->current_company_id),
            'description' => '此邀请码有效期为24小时后失效,且供20人使用',
        ];
        return $data;
    }

    /**
     * 2兑换邀请码
     */
    public function redeemInvitationCode($data)
    {
        $invite_code = $data['invite_code'];
        $company_id = FunctionTool::decrypt_id($data['company_id']);
        $user_id=FunctionTool::decrypt_id($data['user_id']);
        $check = Doorman::check($invite_code);
        if (!$check) {
            return Header("Location: https://www.baidu.com");
        }
        $company = Company::find($company_id);
        $user=User::find($user_id);
        $c_data = [
            'company_id' => $company->id,
            'company_name' => $company->name,
            'user_id'=>$user->id,
            'user_name'=>$user->name,
        ];
        return view('/invite-staff',$c_data);
    }

    /**
     * 3设置用户名和密码或验证用户名和密码(请求数据同用户注册一样)
     */
    public function setUser(Request $request)
    {
        $data = $request->all();
        $telcode_validate=$this->getValidateTool->telcode_validate(['tel'=>$data['tel'],'tel_key'=>$data['tel_key'],'tel_code'=>$data['tel_code']]);//手机号验证
        if($telcode_validate!==true){
            return $telcode_validate;
        }
        try {
            DB::beginTransaction();
            $tel = $data['tel'];
//            $password = $data['password'];
            $company_id = $data['company_id'];
            $user=User::where('tel', $tel)->first();
            if ($user) {
//                $user = User::where('tel', $tel)->where('password', $password)->first();
//                if (!$user) {
//                    return ['status' => 'fail', 'message' => '用户名或密码错误'];
//                }
                $user_id = $user->id;
                $data=[
                    'user_id'=>$user->id,
                    'company_id'=>$company_id,
                    'name'=>array_get($data,'name')==null?$user->name:$data['name'],
                    'tel'=>$user->tel,
                    'email'=>$user->email,
                    'activation'=>1
                ];
            } else {//注册
                $user = $this->register($request->all());
                $user_id = $user->id;
                $data=[
                    'user_id'=>$user->id,
                    'company_id'=>$company_id,
                    'name'=>array_get($data,'name')==null?$user->name:$data['name'],
                    'tel'=>$user->tel,
                    'email'=>$user->email,
                    'activation'=>1
                ];
            }
            //写入(company_user)公司与用户关系
            $count=UserCompany::where('user_id',$user_id)->where('company_id',$company_id)->count();
            if($count>0){
                return ['status' => 'fail', 'message' => '用户已存在本公司!!'];
            }
            DB::table('user_company')->updateOrInsert(['user_id' => $user_id, 'company_id' => $company_id],['user_id' => $user_id, 'company_id' => $company_id,'activation'=>1]);
            //将员工信息存表
            DB::table('user_company_info')->updateOrInsert(['user_id'=>$user_id,'company_id'=>$company_id],$data);
            //获取公司根部门id
            $department_id=Department::where('parent_id',null)->where('company_id',$company_id)->value('id');
            DB::table('user_department')->updateOrInsert(['user_id'=>$user_id,'department_id'=>$department_id],['user_id'=>$user_id,'department_id'=>$department_id]);
            DB::commit();
            $this->departmentRepository->saveInfo($company_id);//更新部门树缓存字段
            return ['status' => 'success', 'message' => '加入成功'];
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            return ['status' => 'fail', 'message' => '服务器错误!!'];
        }
    }

    /**
     * 用户注册的逻辑
     * @param Request $request
     */
    private function register($data)
    {
//        $validator = $this->getValidateTool->register_validate($data);//数据验证
//        if (is_array($validator)) {
//            return json_encode($validator);
//        }
        DB::beginTransaction();//开启事务管理
        try {
            $user = User::create([
                'name' => '用户_' . $data['tel'],
                'tel' => $data['tel'],
                'tel_verified' => 1,
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'email_token' => $this->makeEmailToken(),
            ]);//创建新用户
            $this->userOssTool->makeRootPath($user);
            DB::commit();//提交数据库操作
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();//数据库回滚
            return json_encode(['status' => 'fail', 'message' => '服务器开小差了']);
        }
    }

    /**
     * 生成唯一email_token字段
     */
    private function makeEmailToken()
    {
        $token = str_random(40);
        if ($this->userRepository->checkEmailTokenExsit($token)) {
            return $this->makeEmailToken();
        } else {
            return $token;
        }
    }

    //邀请链接邀请(生成邀请连接)
    public function invitationUrl()//管理员生成邀请连接
    {
        $user=auth('api')->user();
        $generateInvitationCode = Doorman::generate()->uses(100)->expiresIn(7)->make();//最大上限100人,过期时间7天
        $invite_code = $generateInvitationCode[0]->code;//邀请码
        $current_company_id=FunctionTool::encrypt_id($user->current_company_id);
        $user_id=FunctionTool::encrypt_id($user->id);
//        $user_name=$user->name;
//        $company_name=Company::find($user->current_company_id)->name;
        $url='?invite_code='.$invite_code.'&company_id='.$current_company_id.'&user_id='.$user_id;
        return ['status'=>'success','messgae'=>'最大上限100人,过期时间7天','url'=>$url];
    }

    /***********************合作伙伴******************************/
    /**
     * 合作伙伴分类列表
     */
    public function companyPartnerTypes()
    {
        $company_id = auth('api')->user()->current_company_id;
        return DB::table('company_partner_sort')->where('company_id',$company_id)->get()->map(function ($partner){
            return [
                'id'=>FunctionTool::encrypt_id($partner->id),
                'name'=>$partner->name,
                'company_id'=>FunctionTool::encrypt_id($partner->company_id),
            ];
        });
    }

    /**
     * @param $data
     * 增删该合作伙伴类型
     */
    public function companyPartnerTypesOperating($data)
    {
        $operating=array_get($data,'operating');
        $type_id=array_get($data,'type_id')===null?null:FunctionTool::decrypt_id(array_get($data,'type_id'));
        $type_name=array_get($data,'type_name');

        $user=auth('api')->user();
        switch ($operating){
            case 'add':
                $id=DB::table('company_partner_sort')->insertGetId(['name'=>$type_name,'company_id'=>$user->current_company_id]);
                return ['status'=>'success','message'=>'添加成功','id'=>$id];
                break;
            case 'delete':
                $count=DB::table('partner_sort')->where('sort_id',$type_id)->count();
                if($count===0){
                    DB::table('company_partner_sort')->where('company_id',$user->current_company_id)->where('id',$type_id)->delete();
                    return ['status'=>'success','message'=>'删除成功'];
                }else{
                    return ['status'=>'fail','message'=>'该分组下有合作伙伴,删除失败'];
                }
                break;
            case 'alter':
                DB::table('company_partner_sort')->where('company_id',$user->current_company_id)->where('id',$type_id)->update(['name'=>$type_name]);
                return ['status'=>'success','message'=>'修改成功'];
                break;
        }
    }
    /**
     * 公司合作伙伴信息
     */
    public function companyPartner($data)
    {
        $type_id = $data['id'];
        $page_size = $data['page_size'];
        $now_page = $data['now_page'];
        $user = auth('api')->user();
        $company_id=array_get($data,'company_id');
        $company_id=$company_id===null?$user->current_company_id:(gettype($company_id)=='integer'?$company_id:FunctionTool::decrypt_id($company_id));
        if($type_id=='all'){
            return $this->allPartner($company_id,$now_page,$page_size);
        }
        $type_id=gettype($type_id)=='integer'?$type_id:FunctionTool::decrypt_id($type_id);
        $companyPartnerSort=CompanyPartnerSort::where('company_id',$company_id)->where('id','like',$type_id)->get();
        $company=[];
        foreach ($companyPartnerSort as $sort){
            $partners=$sort->partners;//每个分类对应的company_partner数据
            foreach ($partners as $value){
                if($value->company_id==$company_id){
                    $company[]=
                    [
                        'relate_id'=>$value->id,
                        'id'=>$value->inviteCompany->id,
                        'name'=>$value->inviteCompany->name,
                        'address'=>$value->inviteCompany->address,
                        'user_name'=>$value->inviteCompany->creator->name,
                        'user_tel'=>$value->inviteCompany->creator->tel,
                        'user_email'=>$value->inviteCompany->creator->email,
                    ];
                }else{
                    $company[]=
                    [
                        'relate_id'=>$value->id,
                        'id'=>$value->company->id,
                        'name'=>$value->company->name,
                        'address'=>$value->company->address,
                        'user_name'=>$value->company->creator->name,
                        'user_tel'=>$value->company->creator->tel,
                        'user_email'=>$value->company->creator->email,
                    ];
                }
            }
        };
        $count=count($company);
        $data=array_slice($company,($now_page-1)*$page_size,$page_size);
        return ['count'=>$count,'data'=>$data];
    }
    private function allPartner($company_id,$now_page,$page_size)
    {
        $company=[];
        $partners=CompanyPartner::where('company_id',$company_id)->orWhere('invite_company_id',$company_id)->get();//每个分类对应的company_partner数据
        foreach ($partners as $value){
                if($value->company_id==$company_id){
                    $company[]=
                        [
                            'relate_id'=>$value->id,
                            'id'=>$value->inviteCompany->id,
                            'name'=>$value->inviteCompany->name,
                            'address'=>$value->inviteCompany->address,
                            'user_name'=>$value->inviteCompany->creator->name,
                            'user_tel'=>$value->inviteCompany->creator->tel,
                            'user_email'=>$value->inviteCompany->creator->email,
                        ];
                }else{
                    $company[]=
                        [
                            'relate_id'=>$value->id,
                            'id'=>$value->company->id,
                            'name'=>$value->company->name,
                            'address'=>$value->company->address,
                            'user_name'=>$value->company->creator->name,
                            'user_tel'=>$value->company->creator->tel,
                            'user_email'=>$value->company->creator->email,
                        ];
                }
            }
        $count=count($company);
        $data=array_slice($company,($now_page-1)*$page_size,$page_size);
        return ['count'=>$count,'data'=>$data];
    }

    /**
     * 按公司名搜索本公司的合作伙伴
     */
    public function companyPartnerByName($data)
    {
        $name = $data['name'];
        $page_size = $data['page_size'];
        $now_page = $data['now_page'];
        $user = auth('api')->user();
        $comapny_partner = Company::find($user->current_company_id)->comapny_partner->where('name','like',$name)->map(function ($comapny_partner){
            return [
                'id'=>$comapny_partner->id,
                'name'=>$comapny_partner->name,
                'address'=>$comapny_partner->address,
                'user_name'=>$comapny_partner->creator->name,
                'user_tel'=>$comapny_partner->creator->tel,
                'user_email'=>$comapny_partner->creator->email,

            ];
        })->toarray();
        $count=count($comapny_partner);
        $data=array_slice($comapny_partner,($now_page-1)*$page_size,$page_size);
        return ['count'=>$count,'data'=>$data];
    }
    /**
     * 搜索公司合作伙伴(准备邀请时操作)
     */
    public function searchCompanyPartner($name)
    {
        $company_id = auth('api')->user()->current_company_id;
        $companys = Company::where('name','like', '%'.$name.'%')->where('id','!=',$company_id)->get();
        if(count($companys)==0){
            return ['status'=>'fail','message'=>'未找到公司'];
        }
        $companyPartner = $companys->map(function ($company) use ($company_id) {
            $ids=[$company->id,$company_id];
            $partner_records = DB::table('company_partner_record')->whereIn('company_id', $ids)->whereIn('invite_company_id', $ids)->pluck('state')->toarray();
            $operating = in_array(2, $partner_records) ? '等待验证' : (in_array(1, $partner_records) ? '已成为伙伴' : '加合作伙伴');
//            $node_id=DB::table('company_department')->where('company_id',$company_id)->where('parent_id',null)->value('id');
//            $users=$this->departmentTool->getNodeUsers($node_id);
            $user_count=UserCompany::where('company_id',$company->id)->where('activation',1)->count();
            return [
                'id' => $company->id,
                'number'=>$company->number,
                'name' => $company->name,
                'province' => $company->province,
                'verifie_status' => $company->verified,
                'operating' => $operating,
                'company_number'=>$user_count,
            ];
        });
        return ['status'=>'success','data'=>$companyPartner];
    }

    /**
     * 申请加合作伙伴(按钮)
     */
    public function applyAddCompanyPartner($invite_company_id)
    {
        /**********方法见:companyTool--(sendCompanyPartner)***********/
        //        $api->post('/c_send_company_partner','CompanyController@sendCompanyPartner'  );//发起合作伙伴邀请
        //        $api->post('/c_deal_company_partner','CompanyController@dealCompanyPartner'  );//处理合作伙伴邀请
        //        $api->post('/c_get_company_partner','CompanyController@getCompanyPartner'  );//获取某企业的合作伙伴信息
        //        $api->get('/c_get_company_partner_record','CompanyController@getCompanyPartner'  );//获取某企业邀请合作伙伴的记录
    }

    /**
     * 合作伙伴申请列表
     */
    public function companyPartnerApply()
    {
        $current_company_id = auth('api')->user()->current_company_id;
        $partner_records = Company::find($current_company_id)->partner_record;
        $partner_records = $partner_records->map(function ($partner_record) {
//            $operating = $partner_record->state == 2 ? '等待验证' : ($partner_record->state == 1 ? '已同意' : '已拒绝');
            return [
                'id' => $partner_record->id,
                'invite_company_id' => $partner_record->invite_company_id,
                'invite_company_name' => $partner_record->invite_company_name,
                'partner_user_name' => $partner_record->user->name,
                'partner_user_tel' => $partner_record->user->tel,
                'partner_user_email' => $partner_record->user->email,
//                'apply_content' => $partner_record->creator_id,
                'apply_description'=>$partner_record->apply_description,
                'operating' => $partner_record->state,
            ];
        });
        return $partner_records;
    }

    /**
     * 处理合作伙伴申请
     */
    public function dealCompanyPartner()
    {
        /*************方法见:companyTool--(dealCompanyPartner)*************/
        //        $api->post('/c_deal_company_partner','CompanyController@dealCompanyPartner'  );//处理合作伙伴邀请
    }

    /**
     * 接触合作伙伴联系
     */
    public function deleteCompanyPartner($company_id)
    {
        $current_company_id=auth('api')->user()->current_company_id;
        $ids=[$company_id,$current_company_id];
        $partner_id=DB::table('company_partner')->whereIn('company_id',$ids)->whereIn('invite_company_id',$ids)->pluck('id')->toArray();
        DB::table('company_partner')->whereIn('company_id',$ids)->whereIn('invite_company_id',$ids)->delete();
        DB::table('partner_sort')->whereIn('partner_id',$partner_id)->delete();
        DB::table('company_partner_record')->whereIn('company_id',$ids)->whereIn('invite_company_id',$ids)->delete();
        return ['status'=>'success','message'=>'操作成功'];
    }

    /**
     * 批量操作合作伙伴分组
     */
    public function partnerGroupEdit($relate_id,$type_id)
    {
        try{
            DB::beginTransaction();
            $user=auth('api')->user();
            $sort_ids=CompanyPartnerSort::where('company_id',$user->current_company_id)->pluck('id')->toarray();
            DB::table('partner_sort')->whereIn('partner_id',$relate_id)->whereIn('sort_id',$sort_ids)->delete();
            if($type_id==0){
                DB::commit();
                return ['fail'=>'success','message'=>'操作成功'];
            }
            $data=[];
            foreach ($relate_id as $value){
                $data[]=[
                    'partner_id'=>$value,
                    'sort_id'=>$type_id
                ];
            }
            DB::table('partner_sort')->insert($data);
            DB::commit();
            return ['status'=>'success','message'=>'操作成功'];
        }catch (\Exception $exception){
            DB::rollBack();
            return ['status'=>'fail','message'=>'服务器错误!'];
        }

    }
        /*******************外部联系人****************************/
    /**
     * 搜索外部联系人
     */
    public function searchExternalContactUsers($condition)
    {
        $users = User::where('tel', $condition)->Orwhere('name',$condition)->Orwhere('email',$condition)->get();
        if (count($users)==0) {
            return ['status' => 'fail', 'message' => '该用户不存在'];
        }
        $data=[];
        $company_id=auth('api')->user()->current_company_id;
        foreach ($users as $user){
            $count=UserCompany::where('company_id',$company_id)->where('user_id',$user->id)->count();
            if($count>0){
                continue;
            }
            $record=DB::table('company_external_contact')->where('company_id',$company_id)->where('external_contact_id',$user->id)->first();
            $data[] = [
                'id' => $user->id,
                'name' => $user->name,
                'tel' => $user->tel,
                'email'=>$user->email,
                'operating'=>$record==null?'邀请成为外部联系人':($record->status==2?'已邀请,等待验证':($record->status==1?'已经是外部联系人':'邀请成为外部联系人')),
                'created_at' => Carbon::parse($user->created_at)->toDateTimeString(),
            ];
        }
        if(count($data)==0){
            return ['status' => 'fail', 'message' => '该用户存在本公司,不可邀请'];
        }
        return ['status'=>'success','data'=>$data];
    }

    /**
     * 邀请外部联系人
     */
    public function inviteExternalContactUsers($user_id,$description)
    {
        $company_id = auth('api')->user()->current_company_id;
        $user_ids = Company::find($company_id)->users->pluck('id')->toarray();
        if (in_array($user_id, $user_ids)) {
            return ['status' => 'fail', 'message' => '邀请失败,该用户属于本公司人员'];
        }
        $record = DB::table('company_external_contact')->where('company_id', $company_id)->where('external_contact_id', $user_id)->get();
        $status = $record->pluck('status')->toArray();
        if (count($record) > 10 || in_array(0, $status) || in_array(2, $status)) {
            return ['status' => 'fail', 'message' => '已经邀请过等待加入或邀请记录超过10次或已该外部联系人已存在'];
        }
        $user = User::find($user_id);
        if ($user) {
            DB::table('company_external_contact')->insert(['company_id' => $company_id, 'external_contact_id' => $user_id, 'description' => $description]);
            return ['status' => 'success', 'message' => '已发出邀请'];
        } else {
            return ['status' => 'fail', 'message' => '邀请的用户不存在'];
        }
    }

    /**
     * 外部联系公司邀请列表
     */
    public static function applyExternalContactCompanys()
    {
        $user_id = auth('api')->user()->id;
        User::$external_contact_status=2;
        $apply_record = User::find($user_id)->externalContactCompanys->map(function ($record) {
            return [
                'id'=> $record->id,
                'name'=> $record->name,
                'verified'=> $record->verified,
                'number'=> $record->number,
                'address'=> $record->address,
                'reason'=>$record->pivot->description
                ];
        })->toarray();
        return $apply_record;
    }

    /**
     * 处理外部联系公司申请
     */
    public function dealExternalContactUsers(int $company_id, $agreeOrRefuse,$type_id)
    {
        $user_id = auth('api')->user()->id;
        $external_id=DB::table('company_external_contact')->where('company_id', $company_id)
            ->where('external_contact_id', $user_id)
            ->where('status', 2)->value('id');
        if(!$external_id){
            return ['status'=>'fail','message'=>'操作失败'];
        }
        if($agreeOrRefuse=='refuse'){
            DB::table('company_external_contact')->where('company_id', $company_id)
                ->where('external_contact_id', $user_id)
                ->where('status', 2)
                ->delete();
        }elseif ($agreeOrRefuse=='agree'){
            DB::table('company_external_contact')->where('company_id', $company_id)
                ->where('external_contact_id', $user_id)
                ->where('status', 2)
                ->update(['status' => 1]);
        }else{
            return ['status'=>'fail','message'=>'操作失败'];
        }
        if($type_id!=(-1)){//添加分组关系
            DB::table('external_group_relate')->insert([
                'model_id'=>$type_id,
                'model_type'=>ExternalContactType::class,
                'external_id'=>$external_id
            ]);
        }
        return ['status' => 'success', 'message' => '操作成功'];
    }
    /**
     * 处理员工邀请
     */
    public function dealStaffInvite($data)
    {
        $user_id=auth('api')->id();
        $company_id=FunctionTool::decrypt_id($data['company_id']);
        $agreeOrRefuse=$data['agreeOrRefuse'];
        if ($agreeOrRefuse=='agree'){//同意
            UserCompanyInfo::where('user_id',$user_id)->where('company_id',$company_id)->where('activation',0)->update(['activation'=>1]);
            UserCompany::where('user_id',$user_id)->where('company_id',$company_id)->where('activation',0)->update(['activation'=>1]);
            $this->departmentRepository->saveInfo();//更新部门树缓存字段
        }else{
            UserCompanyInfo::where('user_id',$user_id)->where('company_id',$company_id)->where('activation',0)->delete();
            UserCompany::where('user_id',$user_id)->where('company_id',$company_id)->where('activation',0)->delete();
        }
        return ['status'=>'success','message'=>'操作成功'];
    }

    /**
     * @param $data
     * 增删该外部联系人类型
     */
    public function externalContactTypesOperating($data)
    {
        $operating=array_get($data,'operating');
        $type_id=array_get($data,'type_id')===null?null:FunctionTool::decrypt_id(array_get($data,'type_id'));
        $type_name=array_get($data,'type_name');

        $user=auth('api')->user();
        switch ($operating){
            case 'add':
                $id=DB::table('external_contact_type')->insertGetId(['name'=>$type_name,'company_id'=>$user->current_company_id]);
                return ['status'=>'success','message'=>'添加成功','id'=>$id];
                break;
            case 'delete':
                $count=DB::table('external_group_relate')->where('model_id',$type_id)->where('model_type','=','App\Models\ExternalContactType')->count();
                if($count===0){
                    DB::table('external_contact_type')->where('company_id',$user->current_company_id)->where('id',$type_id)->delete();
                    return ['status'=>'success','message'=>'删除成功'];
                }else{
                    return ['status'=>'fail','message'=>'该分组下有合作伙伴,删除失败'];
                }
                break;
            case 'alter':
                DB::table('external_contact_type')->where('company_id',$user->current_company_id)->where('id',$type_id)->update(['name'=>$type_name]);
                return ['status'=>'success','message'=>'修改成功'];
                break;
        }
    }
    /**
     * 外部联系人分类列表
     */
    public function externalContactTypes()
    {
        $company_id = auth('api')->user()->current_company_id;
        return DB::table('external_contact_type')->where('company_id',$company_id)->get()->map(function ($external_contact){
            return [
              'id'=>FunctionTool::encrypt_id($external_contact->id),
              'name'=>$external_contact->name,
              'company_id'=>FunctionTool::encrypt_id($external_contact->company_id),
            ];
        });
    }
    /**
     * 外部联系公司分类列表
     */
    public function externalCompanyTypes()
    {
        $user_id = auth('api')->user()->id;
        return DB::table('external_company_group')->where('user_id',$user_id)->get();
    }
    /**
     * @return mixed
     * 返回外部联系人数据
     */
    public function externalContactUsers($data)
    {
        $page_size = $data['page_size'];
        $now_page = $data['now_page'];
        $type_id = $data['id'];
        if($type_id!='all'){
            $type_id=gettype($type_id)=='integer'?$type_id:FunctionTool::decrypt_id($type_id);
            return $this->departExternalUser($type_id,$now_page,$page_size);
        }
        $user = auth('api')->user();
        $company_id=array_get($data,'company_id');
        $company_id=$company_id===null?$user->current_company_id:(gettype($company_id)=='integer'?$company_id:FunctionTool::decrypt_id($company_id));
        $external_contact = Company::find($company_id)->externalContactUsers->map(function ($external_contact){
            return [
                'id'=>$external_contact->id,
                'name'=>$external_contact->name,
                'tel'=>$external_contact->tel,
                'email'=>$external_contact->email,
            ];
        })->toarray();
        $count=count($external_contact);
        $data=array_slice($external_contact,($now_page-1)*$page_size,$page_size);
        return ['count'=>$count,'data'=>$data];
    }
    private function departExternalUser($type_id,$now_page,$page_size)
    {
        $external_contact = ExternalContactType::find($type_id)->externalGroupRelates->map(function ($relate){
            return [
                'id'=>$relate->user->id,//外部联系人id
                'name'=>$relate->user->name,//外部联系人姓名
                'tel'=>$relate->user->tel,//外部联系人电话
                'email'=>$relate->user->email,//外部联系人email
                'address'=>$relate->user->address,//外部联系人地址
            ];
        })->toarray();
        $count=count($external_contact);
        $data=array_slice($external_contact,($now_page-1)*$page_size,$page_size);
        return ['count'=>$count,'data'=>$data];
    }

    /**
     * 删除外部联系人
     */
    public function deleteExternalUser($user_id)
    {
        $user=auth('api')->user();
        $company_group=[];
        $contact_group=[];
        //此公司拥有的所有的外部联系人分组ids
        $contact_group=DB::table('external_contact_type')->where('company_id',$user->current_company_id)->pluck('id');
        //此用户拥有的所有外部联系公司分组id是
        $company_group=DB::table('external_company_group')->where('user_id',$user_id)->pluck('id');
        //删除公司与外部联系人的关系
        CompanyExternalContact::where('company_id',$user->current_company_id)->where('external_contact_id',$user_id)->delete();
        //删除分组中的关联关系
        DB::table('external_group_relate')->whereIn('model_id',$company_group)->where('external_id',$user_id)->where('model_type','App\Models\ExternalContactType')->delete();
        DB::table('external_group_relate')->whereIn('model_id',$contact_group)->where('external_id',$user->current_company_id)->where('model_type','App\Models\ExternalCompanyGroup')->delete();
        return ['status'=>'success','message'=>'操作成功'];
    }
    /**
     * 删除外部联系公司
     */
    public function deleteExternalCompany($company_id)
    {
        $user=auth('api')->user();
        $company_group=[];
        $contact_group=[];
        //此公司拥有的所有的外部联系人分组ids
        $contact_group=DB::table('external_company_group')->where('company_id',$company_id)->pluck('id');
        //从用户拥有的所有外部联系公司分组id是
        $company_group=DB::table('external_contact_type')->where('user_id',$user->id)->pluck('id');
        //删除公司与外部联系人的关系
        CompanyExternalContact::where('company_id',$company_id)->where('external_contact_id',$user->id)->delete();
        //删除分组中的关联关系
        DB::table('external_group_relate')->whereIn('model_id',$company_group)->where('external_id',$user->id)->where('model_type','App\Models\ExternalContactType')->delete();
        DB::table('external_group_relate')->whereIn('model_id',$contact_group)->where('external_id',$company_id)->where('model_type','App\Models\ExternalCompanyGroup')->delete();
        return ['status'=>'success','message'=>'操作成功'];
    }
    /**
     * @return mixed
     * 返回外部联系公司数据
     */
    public function externalContactCompanys($data)
    {
        $page_size = $data['page_size'];
        $now_page = $data['now_page'];
        $type_id = $data['id'];
        if($type_id!='all'){
            return $this->departExternalCompany($type_id,$now_page,$page_size);
        }
        $user_id = auth('api')->user()->id;
        User::$external_contact_status=1;
        //外部联系公司
        $externalContactCompanys = User::find($user_id)->externalContactCompanys->map(function ($external_company){
            return [
                'id'=>$external_company->id,
                'name'=>$external_company->name,
            ];
        })->toarray();
        $count=count($externalContactCompanys);
        $data=array_slice($externalContactCompanys,($now_page-1)*$page_size,$page_size);
        return ['count'=>$count,'data'=>$data];
    }
    private function departExternalCompany($type_id,$now_page,$page_size)
    {
        $external_contact = ExternalCompanyGroup::find($type_id)->externalGroupRelates->map(function ($relate){
            return [
                'id'=>$relate->company->id,//外部联系公司id
                'name'=>$relate->company->name,//外部联系公司名
            ];
        })->toarray();
        $count=count($external_contact);
        $data=array_slice($external_contact,($now_page-1)*$page_size,$page_size);
        return ['count'=>$count,'data'=>$data];
    }
    /**
     * 批量操作分组(外部联系人或公司)
     * $relate_id 用户ids 或公司ids
     * $type_id 组id
     * $type ='user',为编辑外部联系人,$type ='company',为编辑外部联系公司,
     */
    public function externalGroupEdit($relate_id,$type_id,$type)
    {
        try{
            DB::beginTransaction();
            if($type=='user'){//为编辑外部联系人
                $model_type='App\Models\ExternalContactType';
            }elseif ($type=='company'){//为编辑外部联系公司
                $model_type='App\Models\ExternalCompanyGroup';
            }else{
                return ['status'=>'fail','message'=>'操作失败'];
            }
            DB::table('external_group_relate')->whereIn('external_id',$relate_id)->where('model_type','=',$model_type)->delete();
            if($type_id==0){
                DB::commit();
                return ['status'=>'success','message'=>'操作成功'];
            }
            $data=[];
            foreach ($relate_id as $value){
                $data[]=[
                    'model_id'=>$type_id,
                    'model_type'=>$model_type,
                    'external_id'=>$value,
                ];
            }
            DB::table('external_group_relate')->insert($data);
            DB::commit();
            return ['status'=>'success','message'=>'操作成功'];
        }catch (\Exception $exception){
            DB::rollBack();
            return ['status'=>'fail','message'=>'服务器错误!'];
        }

    }
    /**
     * tel email name 模糊查询外部联系人
     */
    public function externalUserByName($condition)
    {
        $company_id = auth('api')->user()->current_company_id;
        $user_ids=CompanyExternalContact::where('company_id',$company_id)->where('status',1)->pluck('external_contact_id')->toarray();
        $users = User::whereIn('id',$user_ids)->where('tel', $condition)->Orwhere('name',$condition)->Orwhere('email',$condition)->get();
        if (!$users) {
            return ['status' => 'fail', 'message' => '该用户不存在'];
        }
        $data=[];
        foreach ($users as $user){
            $data[] = [
                'id' => $user->id,
                'name' => $user->name,
                'tel' => $user->tel,
                'email'=>$user->email,
                'created_at' => Carbon::parse($user->created_at)->toDateTimeString(),
            ];
        }
        return $data;
    }
    /********************组织排序************************************/
    /**`
     * 公司所有部门
     */
    public function descendants()
    {
        $company_id = auth('api')->user()->current_company_id;
        $node_id = Department::where('parent_id', null)->where('company_id', $company_id)->value('id');
        $department = Department::defaultOrder()->where('parent_id', $node_id)->descendantsOf($node_id);
        return ['count' => 0, 'data' => $department];
    }

    /**
     * 部门排序(操作部门排序时,每次拖拽都要请求一次)
     */
    public function departmentOrdering($data)
    {
        $oldOrder = $data['oldOrder'];
        $newOrder = $data['newOrder'];
        $node_id = $data['node_id'];
        $node = Department::find($node_id);
        $move = $oldOrder - $newOrder;

        if ($move > 0) {//上移
            $moveUp = $move;
            $node->up($moveUp);
        } else {
            $moveDown = $newOrder - $oldOrder;
            $node->down($moveDown);
        }
        $this->departmentRepository->saveInfo();//更新部门树缓存字段
        return ['status' => 'success', 'message' => '操作成功'];
    }

    /**
     * @param $data
     * 职务排序(可以拖拽完之后,一同保存顺序)
     */
    public function jobOrdering($data)
    {
        foreach ($data['sort_data'] as $k => $v) {//$k(职务id)=>$v(顺序号)
            $k = FunctionTool::decrypt_id($k);
            Role::where('id', $k)->update(['sort' => $v]);
        }
        return ['status' => 'success', 'message' => '保存成功'];
    }

    /********************操作日志***************************/
    /**
     * 日志模块类型(日志左侧列表)
     */
    public function logModuleType()
    {
        return config('companylog.module');
    }
    /**
     * 日志操作类型
     */
    public function logOperationType($operation_type)
    {
        return ['created_at'=>'添加','deleted_at'=>'删除','edit'=>'修改'];
    }
    /**
     * @param $data
     * @return \Illuminate\Support\Collection
     * 按条件查询日志
     */
    public function searchOperationLog($data)
    {
        $company_id = auth('api')->user()->current_company_id;
        $operation_type = array_get($data, 'operation_type','%');
        $operator_id = array_get($data, 'operator_id','%');
        $content = array_get($data, 'content','%');
        $module_type = array_get($data, 'module_type',[]);
        $table = array_get($module_type, 'table','roles');
        $model = array_get($module_type, 'model',Role::class);
        $now_page = array_get($data, 'now_page',1);
        $page_size = array_get($data, 'page_size',10);
        $start_time = array_get($data, 'start_time',date('Y-m-d H:i:s', 1552009949));
        $end_time = array_get($data, 'end_time',date('Y-m-d H:i:s', time()));
        $offset = ($now_page-1)*$page_size;

        //获取公司拥有的模型ids
        switch ($table){
            case 'roles':
                $model_ids=DB::table('model_has_role')->where('model_type','App\Models\Company')->pluck('role_id')->toarray();
                break;
            case '':
                $model_ids=[];
                break;
            default:
                $model_ids=DB::table($table)->where('company_id',$company_id)->pluck('id')->toarray();
                break;
        }

//        $operation_log=Company::find($company_id)->operationLogs->where('module_type','like',$module_type);
        if($operation_type!='edit'){
            $operation_log = DB::table('revisions')
                ->where('revisionable_type',$model)//模块类型
                ->whereIn('revisionable_id',$model_ids)
                ->where('key','like',$operation_type)//操作类型(增删)
                ->where('user_id','like', $operator_id)//操作人id
                ->where('new_value','like',$content)//内容
                ->whereBetween('created_at',[$start_time,$end_time])//起始时间
                ->offset($offset)
                ->limit($page_size)
                ->get();
        }else{
            $operation_log = DB::table('revisions')
                ->where('operation_type','!=','created_at')//操作类型(改)
                ->where('operation_type','!=','deleted_at')//操作类型(改)
                ->where('revisionable_type',$model)//模块类型
                ->whereIn('revisionable_id',$model_ids)
                ->where('user_id','like', $operator_id)//操作人id
                ->where('new_value','like',$content)//内容
                ->whereBetween('created_at',[$start_time,$end_time])//起始时间
                ->offset($offset)
                ->limit($page_size)
                ->get();
        }
        return $operation_log;
    }
//    /**
//     * @param $data
//     * @return \Illuminate\Support\Collection
//     * 按条件查询日志
//     */
//    public function searchOperationLog($data)
//    {
//        $company_id = auth('api')->user()->current_company_id;
//        $terminal_equipment = array_get($data, 'terminal_equipment','%');
//        $operation_type = array_get($data, 'operation_type','%');
//        $operator_id = array_get($data, 'operator_id','%');
//        $content = array_get($data, 'content','%');
//        $module_type = array_get($data, 'module_type','%');
//        $now_page = array_get($data, 'now_page',1);
//        $page_size = array_get($data, 'page_size',10);
//        $start_time = array_get($data, 'start_time',date('Y-m-d H:i:s', 1552009949));
//        $end_time = array_get($data, 'end_time',date('Y-m-d H:i:s', time()));
//        $offset = ($now_page-1)*$page_size;
//
////        $operation_log=Company::find($company_id)->operationLogs->where('module_type','like',$module_type);
//        $operation_log = DB::table('company_operation_log')
//            ->where('company_id', $company_id)
//            ->where('module_type','like',$module_type)//模块类型
//            ->where('terminal_equipment','like',$terminal_equipment)//终端设置
//            ->where('operation_type','like',$operation_type)//操作类型
//            ->where('operator_id', 'like', $operator_id)//操作人id
//            ->where('content','like',$content)//内容
//            ->whereBetween('create_time',[$start_time,$end_time])//起始时间
//            ->offset($offset)
//            ->limit($page_size)
//            ->get();
//        return $operation_log;
//    }

/***********************应用设置****************************/
    /**
     * @return array
     * 公司已开启的功能模块
     */
    public function companyFuns()
    {
        $company_id=auth('api')->user()->current_company_id;
        $funs = Company::find($company_id)->funs;
        $f=[];
        if(count($funs)>0) {
            foreach ($funs as $fun) {
                if($fun->is_enable==1){
                    $fun->per_sort;
                    $f[] = $fun;
                }
            }
        }
        return ['status'=>'success','data'=>$f];
//        $per=[];
//        if(count($funs)>0){
//            foreach ($funs as $fun){
//                $pers=$fun->per_sort->pers;
//                $per[]=$pers;
//            }
//        }
//        return $per;
    }
    /**
     * @param $data
     * 设置功能是否开启
     */
    public function setCompanyFun($data)
    {
        $id=$data['id'];
        $is_enable=$data['is_enable'];
        $per_id=$data['per_id'];
        if($per_id==1){
            return ['status'=>'fail','message'=>'操作失败,系统管理功能不可设置'];
        }
        $count=CompanyHasFun::where('id',$id)->update(['is_enable'=>$is_enable]);
        if($count>0){
            return ['status'=>'success','message'=>'操作成功'];
        }else{
            return ['status'=>'fail','message'=>'操作失败'];
        }
    }

    /**
     * @return array
     * 展示用户功能模块
     */
    public function FunShow()
    {
        $user=auth('api')->user();
        $per=RoleAndPerTool::get_user_c_per($user->id,$user->current_company_id);
        $funs = Company::find($user->current_company_id)->funs;
        $f=[];
        if(count($funs)>0) {
            foreach ($funs as $fun) {
                $per_array=$fun->per_sort->pers->pluck('name')->toarray();
                $common=array_intersect($per,$per_array);
                if($fun->is_enable==1&&count($common)>0){
                    $fun->per_sort;
                    $f[] = $fun;
                }
            }
        }
        return $f;
    }

    //后台管理方法测试
    public function test($request)
    {
        dd(CompanyBasisLimitTool::staffLimit(1));
    }
    /**
 * @param $user_id
 * @param $company_id
 * 用户账号在公司是否被冻结
 */
    public static function freezeUser($user_id,$company_id)
    {
        return DB::table('user_company')->where('user_id',$user_id)->where('company_id',$company_id)->value('is_enable');
    }
    /**
     * 员工在公司下的信息
     */
    public static function companyUesr($users,$activation,$company_id)
    {
        $user_ids=$users->pluck('id')->toArray();
        return DB::table('user_company_info')
            ->whereIn('user_id',$user_ids)
            ->where('activation',$activation)
            ->where('company_id',$company_id)
            ->get();
    }
    /**
     * 用户注册成功时,查看是否被邀请过,如果有被邀请过,则修改相关信息
     */
    public static function registerSuccess($user)
    {
        $user_tel=$user->tel;
        $user_id=$user->id;
        //添加与公司的关系
        DB::table('user_company')->where('user_id',$user_tel)->update(['user_id'=>$user_id]);
        //将员工信息存表
        DB::table('user_company_info')->where('user_id',$user_tel)->update(['user_id'=>$user_id]);
        //插入用户与部门的关系
        DB::table('user_department')->where('user_id',$user_tel)->update(['user_id'=>$user_id]);
        //为用户添加职务
        DB::table('company_user_role')->where('user_id',$user_tel)->update(['user_id'=>$user_id]);
        return true;
    }

    /**
     * 企业基础数据使用情况
     */
    private function useNumber($type,$use_number,$company_id)
    {
        switch ($type){
            case 'sms':
                return $use_number;
                break;
            case 'voice':
                return $use_number;
                break;
            case 'e-mail':
                return $use_number;
                break;
            case 'partner':
                $partner=DB::table('company_partner')
                    ->where('company_id',$company_id)
                    ->orWhere('invite_company_id',$company_id)
                    ->where('status',1);
                $partner_count=array_unique(array_merge($partner->pluck('company_id')->toArray(), $partner->pluck('invite_company_id')->toArray()));
                return count($partner_count);
                break;
            case 'external_contact':
                $external_contact=DB::table('company_external_contact')
                    ->where('company_id',$company_id)
                    ->where('status',1)
                    ->count();
                return $external_contact;
                break;
            case 'staff_number':
                $staff=UserCompany::where('company_id',$company_id)->count();
                return $staff;
                break;
        }
    }

}