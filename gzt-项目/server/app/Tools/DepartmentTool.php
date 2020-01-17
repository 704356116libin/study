<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;
use App\Http\Resources\department\DepartmentSimpleResource;
use App\Http\Resources\department\DepartmentSingleResource;
use App\Http\Resources\department\ExternalContactResource;
use App\Http\Resources\department\PartnerResource;
use App\Http\Resources\user\UserSimpleResource;
use App\Interfaces\DepartmentInterface;
use App\Models\Company;
use App\Models\CompanyPartner;
use App\Models\CompanyPartnerSort;
use App\Models\Department;
use App\Models\ExternalContactType;
use App\Models\PerSort;
use App\Models\Role;
use App\Models\User;
use App\Models\UserCompany;
use App\Repositories\BasicRepository;
use App\Repositories\CompanyDepartmentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Tools\NotifyTool;
/**
 * 部门工具类
 */
class DepartmentTool implements DepartmentInterface
{
    static private $departmentTool;
    private $departmentRepository;
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
        $this->departmentRepository=CompanyDepartmentRepository::getDepartmentRepository();
    }
    /**
     * 单例模式
     */
    static public function getDepartmentTool(){
        if(self::$departmentTool instanceof self)
        {
            return self::$departmentTool;
        }else{
            return self::$departmentTool = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 创建一个根节点
     * @param $data
     * @return mixed
     */
    public function addRootNode(array $data)
    {
        return Department::create($data)->saveAsRoot();
    }
    /**
     * 某个节点后追加节点
     * @param Department $parentNode
     * @param array $data
     * @return bool
     */
    public function appendNode(array $data)
    {
        $data['company_id']=auth('api')->user()->current_company_id;
        $parentNode=Department::find(FunctionTool::decrypt_id($data['node_id']));
        if(!is_null($parentNode)){
             $parentNode->appendNode(Department::create(collect($data)->except('node_id')->toArray()));
             $this->departmentRepository->saveInfo();//更新部门树缓存字段
//             $this->departmentRepository->makeDepartmentInfo(auth('api')->user()->current_company_id);//添加部门成功后,更新部门信息存储表(company_department_info)
             return json_encode(['status'=>'success', 'message'=>'节点添加成功']);
        }else{
            return json_encode(['status'=>'fail', 'message'=>'目标节点不存在']);
        }
    }
    /**
     * 拿到某个公司完整的部门树
     * @param $company_id
     * @return mixed
     */
    public function getAllTree(Request $request)
    {
        $user=auth('api')->user();
        $company_id=$request->get('company_id');
        $activation=$request->get('activation');
        $activation=$activation===null?1:($activation==='0'?0:($activation==='1'?1:2));
        $company_id=is_null($company_id)?$user->current_company_id:FunctionTool::decrypt_id($company_id);
//        return $this->departmentRepository->makeDepartmentInfo($company_id,$activation);
        if($activation!==1){//返回所有的员工(已激活激活的)
            return $this->departmentRepository->makeDepartmentInfo($company_id,$activation);
        }
        if($this->departmentRepository->checkExsitDepartmentInfo($company_id)){
            return $this->departmentRepository->getDepartmentInfo($company_id);
        }else{
           return $this->departmentRepository->makeDepartmentInfo($company_id,$activation);
        }
    }
    /**
     * 获取合作伙伴树
     */
    public function getAllPartner(Request $request)
    {
        $user=auth('api')->user();
        $company_id=$request->get('company_id');
        $company_id=is_null($company_id)?$user->current_company_id:FunctionTool::decrypt_id($company_id);
        $type=DB::table('company_partner_sort')->where('company_id',$company_id)->get();
        PartnerResource::$company_id=$company_id;
        return PartnerResource::collection($type)->toArray(1);
    }
    public static function allPartner($type_id,$company_id)
    {
        $companyPartnerSort=CompanyPartnerSort::where('company_id',$company_id)->where('id',$type_id)->get();
        $company=[];
        foreach ($companyPartnerSort as $sort){
            $partners=$sort->partners;//每个分类对应的company_partner数据
            foreach ($partners as $value){
                if($value->company_id==$company_id){
                    $company[]=
                        [
                            'relate_id'=>FunctionTool::encrypt_id($value->id),
                            'id'=>FunctionTool::encrypt_id($value->inviteCompany->id),
                            'name'=>$value->inviteCompany->name,
                            'address'=>$value->inviteCompany->address,
                            'user_name'=>$value->inviteCompany->creator->name,
                            'user_tel'=>$value->inviteCompany->creator->tel,
                            'user_email'=>$value->inviteCompany->creator->email,
                        ];
                }else{
                    $company[]=
                        [
                            'relate_id'=>FunctionTool::encrypt_id($value->id),
                            'id'=>FunctionTool::encrypt_id($value->company->id),
                            'name'=>$value->company->name,
                            'address'=>$value->company->address,
                            'user_name'=>$value->company->creator->name,
                            'user_tel'=>$value->company->creator->tel,
                            'user_email'=>$value->company->creator->email,
                        ];
                }
            }
        };

        return $company;
    }
    /**
     * 获取外部联系人树
     */
    public function getAllExternalContact(Request $request)
    {
        $user=auth('api')->user();
        $company_id=$request->get('company_id');
        $company_id=is_null($company_id)?$user->current_company_id:FunctionTool::decrypt_id($company_id);
        $type= DB::table('external_contact_type')->where('company_id',$company_id)->get();
        return ExternalContactResource::collection($type)->toArray(1);
    }
    public static function allExternalContact($type_id)
    {
        $external_contact = ExternalContactType::find($type_id)->externalGroupRelates->map(function ($relate){
            return [
                'id'=>FunctionTool::encrypt_id($relate->user->id),//外部联系人id
                'name'=>$relate->user->name,//外部联系人姓名
                'tel'=>$relate->user->tel,//外部联系人电话
                'email'=>$relate->user->email,//外部联系人email
                'address'=>$relate->user->address,//外部联系人地址
            ];
        })->toarray();
        return $external_contact;
    }
    /**
     * @param Request $request
     * 获取公司部门树和合作伙伴树和外部联系人树
     */
    public function getCompanyAll(Request $request)
    {
        $getAllTree=$this->getAllTree($request);
        $getAllPartner=$this->getAllPartner($request);
        $getAllExternalContact=$this->getAllExternalContact($request);
        $companyUnderPartnerContact=$this->companyUnderPartnerContact($request);
//        $getAllPartner[]=$companyUnderPartnerContact['company'];
//        $getAllExternalContact[]=$companyUnderPartnerContact['external_contact'];
        return ['getAllTree'=>json_decode($getAllTree),'getAllPartner'=>['category'=>$getAllPartner,'partner'=>$companyUnderPartnerContact['company']],'getAllExternalContact'=>['category'=>$getAllExternalContact,'external_contact'=>$companyUnderPartnerContact['external_contact']]];
    }

    /**
     * 获取公司下单独的合作伙伴和外部联系人
     */
    private function companyUnderPartnerContact(Request $request)
    {
//        $user=auth('api')->user();
        $user=auth('api')->user();
        $company_id=$request->get('company_id');
        $company_id=is_null($company_id)?$user->current_company_id:FunctionTool::decrypt_id($company_id);
        $partner_ids_arr=DB::select('select partner_id from partner_sort where sort_id in (select company_partner_sort.id from company_partner_sort where company_id=?)',[$company_id]);
        $company=[];
        $partner_ids=[];
        foreach ($partner_ids_arr as $v){
            $partner_ids[]=$v->partner_id;
        }
        $partners=CompanyPartner::where('company_id',$company_id)->orWhere('invite_company_id',$company_id)->get();//每个分类对应的company_partner数据
        foreach ($partners as $value){
            if(!in_array($value->id,$partner_ids)){
                if($value->company_id==$company_id){
                    $company[]=
                        [
                            'relate_id'=>$value->id,
                            'id'=>FunctionTool::encrypt_id($value->inviteCompany->id),
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
                            'id'=>FunctionTool::encrypt_id($value->company->id),
                            'name'=>$value->company->name,
                            'address'=>$value->company->address,
                            'user_name'=>$value->company->creator->name,
                            'user_tel'=>$value->company->creator->tel,
                            'user_email'=>$value->company->creator->email,
                        ];
                }
            }
        }
        $user_ids=DB::select('select external_group_relate.model_id from external_group_relate where external_group_relate.model_type=? and external_group_relate.external_id in (select external_contact_type.id from external_contact_type where company_id=?)',[User::class,$company_id]);
        $users=[];
        foreach ($user_ids as $v){
            $users[]=$v->partner_id;
        }
        $external_contact = Company::find($company_id)->externalContactUsers->map(function ($external_contact) use ($users){
            if(!in_array($external_contact->id,$users)){
                return [
                    'id'=>FunctionTool::encrypt_id($external_contact->id),
                    'name'=>$external_contact->name,
                    'tel'=>$external_contact->tel,
                    'email'=>$external_contact->email,
                ];
            }
        })->toarray();
        return ['company'=>$company,'external_contact'=>$external_contact];
    }
    /**
     * 拿到某个节点的子树
     * @param $node_id
     * @return mixed
     */
    public function getNodeDescendantsTree($node_id)
    {
        return $this->departmentRepository->getNodeDescendantsTree(FunctionTool::decrypt_id($node_id));
    }
    /**
     * 给某个部门添加员工
     * @param $user_ids:用户ids
     * @param $department_id;目标部门id
     */
    public function addUserToDepartment(array $user_ids, int $department_id)
    {
        $user_ids=FunctionTool::decrypt_id_array($user_ids);
        $this->departmentRepository->saveInfo();//更新部门树缓存字段
        return Department::find($department_id)->users()->attach($user_ids);
    }
    /**
     * 移除某个部门下的指定员工
     * @param $user_ids
     * @param $department_id
     */
    public function removeDepartmentUser($user_ids, $department_id)
    {
        $user_ids=FunctionTool::decrypt_id_array($user_ids);
        $this->departmentRepository->saveInfo();//更新部门树缓存字段
        return Department::find($department_id)->users()->detach($user_ids);//移除部门与用户的关联
    }
    /**
     * 获取某些节点下的所有员工id(包括子节点)
     */
    public function getNodesUsers($node_ids){
        $data=[];
        foreach ($node_ids as $node_id){
           $data=array_merge($data,$this->getNodeUsers($node_id));
        }
        return $data;
    }
    /**
     * 获取某个节点下的所有员工id(包括子节点)
     */
    public function getNodeUsers($node_id){
        $data=[];
        $node=Department::find($node_id);
        if(is_null($node)){
            return $data;
        }
        $users=$node->users;
        if(count($users)!=0){
            foreach ($users as $user){
                $data[]=$user->id;
            }
        }
        if($node->isLeaf()){
           return $data;
        }else{
            $node_ids=$node->descendants->pluck('id')->toArray();
            return array_merge($data,$this->getNodesUsers($node_ids));
        }
    }
    /**
     * 保存员工信息(添加员工)
     */
    public function saveUserDate($data)
    {
        try{
            DB::beginTransaction();
            $company_id=auth('api')->user()->current_company_id;
            $department_id=array_get($data,'department_id')===null?false:array_get($data,'department_id');
            $email=array_get($data,'email');
            $tel=array_get($data,'tel');
            $role_ids=array_get($data,'role_ids');//角色ids
            $data['role_ids']=$role_ids==null?$role_ids:json_encode($role_ids);
            if(!$department_id){
                $department_id=Department::where('parent_id',null)->where('company_id',$company_id)->value('id');
            }else{
                $department_id=FunctionTool::decrypt_id($department_id);
            }
            //账号,手机号,邮箱密码格式验证
            if (!preg_match("/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims",$email)) {
                return ['status'=>'fail','message'=>'邮箱格式错误'];
            }
            if (!preg_match("/^1[34578]\d{9}$/ims",$tel)) {
                return ['status'=>'fail','message'=>'手机号格式错误'];
            }
            $user=User::where('tel',$tel)->first();
            if($user){//用户已存在
                $user_id=$user->id;
                //判断用户是否已在该公司
                $count=DB::table('user_company')->where('user_id',$user_id)->where('company_id',$company_id)->count();
                if($count!=0){
                    return ['status'=>'fail','message'=>'添加失败,用户已在该公司'];
                }
                $activation=0;
            }else{//用户不存在
//                return ['status'=>'fail','message'=>'未注册,请先使用该手机号注册工作通'];
                $user_id=$tel;
                //判断用户是否已在该公司
                $count=DB::table('user_company')->where('user_id',$user_id)->where('company_id',$company_id)->count();
                if($count!=0){
                    return ['status'=>'fail','message'=>'添加失败,用户已在该公司'];
                }
                $activation=0;
            }
            //添加员工与公司的关系
            DB::table('user_company')->updateOrInsert(['user_id'=>$user_id,'company_id'=>$company_id],['user_id'=>$user_id,'company_id'=>$company_id,'activation'=>$activation]);
            //将员工信息存表
            $data['department_id']=$department_id;
            $data['activation']=$activation;
            DB::table('user_company_info')->updateOrInsert(['user_id'=>$user_id,'company_id'=>$company_id],$data);
            //插入用户与部门的关系
            DB::table('user_department')->updateOrInsert(['user_id'=>$user_id,'department_id'=>$department_id],['user_id'=>$user_id,'department_id'=>$department_id,'is_main'=>0]);
            //为用户添加职务
            if($role_ids){
                $insert_data=[];
                $role_ids=FunctionTool::decrypt_id_array($role_ids);
                foreach ($role_ids as $role_id) {
                    $insert_data[] = [
                        'company_id' => $company_id,
                        'user_id' => $user_id,
                        'role_id' => $role_id
                    ];
                }
                DB::table('company_user_role')->insert($insert_data);
            }
            $this->departmentRepository->saveInfo();//更新部门树缓存字段
            DB::commit();
            return ['status'=>'success','message'=>'添加成功'];
        }catch (\Exception $e){
            DB::rollBack();
            dd($e);
            return ['status'=>'fail','message'=>'服务器错误!!'];
        }
    }
    /**
     * 手机号或邮箱添加员工
     */
    public function addStallByTel($telOrEmails)
    {
        try{
            DB::beginTransaction();
            $company_id=auth('api')->user()->current_company_id;
            $department_id=Department::where('parent_id',null)->where('company_id',$company_id)->value('id');
            $i=0;
            foreach ($telOrEmails as $telOrEmail){
                $i++;
                if($i>10){//限制手机号每次不超过数量
                    break;
                }
                //账号,手机号,邮箱密码格式验证
                if (!preg_match("/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims",$telOrEmail)&&!preg_match("/^1[34578]\d{9}$/ims",$telOrEmail)) {
                    return ['status'=>'fail','message'=>'格式错误'];
                }
                $user=User::where('tel',$telOrEmail)->orwhere('email',$telOrEmail)->first();
                if($user){//用户已存在
                    $user_id=$user->id;
                    //判断用户是否已在该公司
                    $count=DB::table('user_company')->where('user_id',$user_id)->where('company_id',$company_id)->count();
                    if($count!=0){
                        continue;
                    }
                    $activation=0;
                    $data=[
                        'user_id'=>$user->id,
                        'company_id'=>$company_id,
                        'name'=>$user->name,
                        'tel'=>$user->tel,
                        'email'=>$user->email,
                        'department_id'=>$department_id,
                        'activation'=>0,
                    ];
                }else{//用户不存在
//                    return ['status'=>'fail','message'=>$telOrEmail.'未注册,请先使用该手机号注册工作通'];
                    $user_id=$telOrEmail;
                    $activation=0;
                    $data=[
                        'user_id'=>$user_id,
                        'company_id'=>$company_id,
                        'name'=>'匿名',
                        'tel'=>$user_id,
                        'department_id'=>$department_id,
                        'activation'=>0,
                    ];
                }
                //添加员工与公司的关系
                $result = DB::table('user_company')->updateOrInsert(['user_id'=>$user_id,'company_id'=>$company_id],['user_id'=>$user_id,'company_id'=>$company_id,'activation'=>$activation]);
                //将员工信息存表
                DB::table('user_company_info')->updateOrInsert(['user_id'=>$user_id,'company_id'=>$company_id],$data);
                //插入用户与部门的关系
                DB::table('user_department')->updateOrInsert(['user_id'=>$user_id,'department_id'=>$department_id],['user_id'=>$user_id,'department_id'=>$department_id,'is_main'=>0]);
                //当用户存在 进行通知
                if($user){
                    $single_data = DynamicTool::getSingleListData(UserCompany::class, 1, 'company_id', 0,
                        '通知:' . '企业邀请', Company::find($company_id)->name.' 邀请您加入~', $result->created_at);
                    NotifyTool::publishNotify([$user->id],$user->current_company_id, $result, ['need_notify'=>1], $single_data,[]);//此处方法顺序待调整
                }

            }
            $this->departmentRepository->saveInfo();//更新部门树缓存字段
            DB::commit();
            return ['status'=>'success','message'=>'添加成功'];
        }catch (\Exception $e){
            DB::rollBack();
            return ['status'=>'fail','message'=>'服务器错误!!'];
        }
    }

    /**
     * 员工信息(抽屉展示)
     */
    public function userDetail($data)
    {
        $current_company_id=auth('api')->user()->current_company_id;
        $user_id=FunctionTool::decrypt_id(array_get($data,'user_id'));
        $company_id=array_get($data,'company_id')==null?$current_company_id:FunctionTool::decrypt_id(array_get($data,'company_id'));
        $user_data=DB::table('user_company_info')->where('user_id',$user_id)->where('company_id',$company_id)->first();
        $data=[
            'id'=>$user_data->id,
            'user_id'=>$user_data->user_id,
            'company_id'=>$user_data->company_id,
            'name'=>$user_data->name,
            'sex'=>$user_data->sex,
            'tel'=>$user_data->tel,
            'email'=>$user_data->email,
            'role_ids'=>json_decode($user_data->role_ids),
            'remarks'=>$user_data->remarks,
            'address'=>$user_data->address,
            'roomNumber'=>$user_data->roomNumber,
            'activation'=>$user_data->activation,
            'department_id'=>$user_data->department_id,
        ];
        return ['status'=>'success','data'=>$data];
    }
    /**
     * 编辑员工信息
     */
    public function editUserDetail($data)
    {
        try{
            DB::beginTransaction();
            $department_id=FunctionTool::decrypt_id(array_get($data,'department_id'));
//            $department_id=FunctionTool::decrypt_id(array_get($data,'node_id'));

            $email=array_get($data,'email');
            $tel=array_get($data,'tel');
            if(array_get($data,'role_ids')!=null){
                $role_ids=FunctionTool::decrypt_id_array(array_get($data,'role_ids'));//角色ids
            }else{
                $role_ids=null;
            }
            $data['role_ids']=$role_ids==null?null:json_encode(array_get($data,'role_ids'));
            $old_department_id=FunctionTool::decrypt_id(array_get($data,'old_node_id'));//原部门id
            unset($data['old_node_id']);
//            unset($data['node_id']);
//            $data['department_id']=$department_id;
            $current_company_id=auth('api')->user()->current_company_id;
            $user_id=array_get($data,'user_id');
            $company_id=array_get($data,'company_id')==null?$current_company_id:array_get($data,'company_id');
            //账号,手机号,邮箱密码格式验证
            if (!preg_match("/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims",$email)) {
                return ['status'=>'fail','message'=>'邮箱格式错误'];
            }
            if (!preg_match("/^1[3456789][0-9]{9}$/",$tel)) {
                return ['status'=>'fail','message'=>'手机号格式错误'];
            }
            $count=DB::table('user_company_info')->where('tel',array_get($data,'tel'))->count();
            if($count==0){
                return ['status'=>'fail','message'=>'手机号有误,编辑失败'];
            }
//        $user_data=UserCompany::where('company_id',$company_id)->where('user_id',$user_id)->update(json_encode($data));
            //将员工信息存表
            DB::table('user_company_info')->updateOrInsert(['user_id'=>$user_id,'company_id'=>$company_id],$data);
            //插入用户与部门的关系
            DB::table('user_department')->updateOrInsert(['user_id'=>$user_id,'department_id'=>$old_department_id],['user_id'=>$user_id,'department_id'=>$department_id,'is_main'=>0]);
            //为用户添加职务
            if($role_ids){
                $insert_data=[];
                foreach ($role_ids as $role_id) {
                    $insert_data[] = [
                        'company_id' => $company_id,
                        'user_id' => $user_id,
                        'role_id' => $role_id
                    ];
                }
                //清除原角色
                DB::table('company_user_role')->where('user_id',$user_id)->where('company_id',$company_id)->delete();
                DB::table('company_user_role')->insert($insert_data);
            }
            //企业超级管理员权限至少有一人拥有
            if(auth('api')->user()->id==$user_id){
                if(!RoleAndPerTool::user_has_c_per($user_id,$company_id,['c_super_manage_per'],'all')){
                    return ['status'=>'fail','message'=>'企业超级管理员权限至少有一人拥有'];
                }
            }
            $this->departmentRepository->saveInfo();//更新部门树缓存字段
            DB::commit();
            return ['status'=>'success','message'=>'操作成功'];
        }catch (\Exception $exception){
            DB::rollBack();
            dd($exception->getMessage());
            return ['status'=>'fail','message'=>'服务器错误!'];
        }
    }
    /**
     * 后台管理系统-添加员工
     *
     */
    public function addStaff($department_id,array $user_ids){
        $user_ids=FunctionTool::decrypt_id_array($user_ids);
        $this->departmentRepository->saveInfo();//更新部门树缓存字段
        return Department::find($department_id)->attach($user_ids);
    }
    /**
     * 后台管理系统-部门列表(根据节点id,获取包含自己的所有节点)
     */
    public function departmentList($company_id){
        try{
            $department_id = $this->departmentRepository->rootNodeByCompanyId($company_id);
            if($department_id){
//                $resource1 = Department::descendantsAndSelf(6)->toTree()[0];
//                $departmentResource = DepartmentResource::collection($resource1);
//                $departList = $departmentResource->departList($resource1);
                $departmentResource=$this->getUserId(1);
                dd($departmentResource);
                return $this->departmentRepository->getNodeChildrens($department_id);
            }else{
                return '未找到该节点';
            }
        }catch (\Exception $e){
            return '服务器错误!!';
        }
    }
    /**
     * 获取单个部门详细信息--(子部门,拥有的人员)
     * @param $department_id
     */
    public function departmentDetail($node_id,$page_size,$now_page,$is_enable,$company_id,$activation){
        $start=($now_page-1)*$page_size;
        $node_id=FunctionTool::decrypt_id($node_id);
        $node=Department::where('id',$node_id)->get();
        $company_id=$company_id==null?$node[0]->company_id:$company_id;//拿到所属企业的id
        if($activation!='2'){
            $activation=$activation=='0'?0:1;
            $users=DB::select('SELECT * FROM user_company_info WHERE activation=? AND company_id=? AND user_id IN (SELECT user_id FROM user_department WHERE department_id=?)
         AND user_id IN (SELECT user_id FROM user_company WHERE company_id=? AND is_enable=? ) ORDER BY id ASC LIMIT ?,?',[$activation,$company_id,$node_id,$company_id,$is_enable,$start,$page_size]);
        }else{
            $users=DB::select('SELECT * FROM user_company_info WHERE company_id=? AND user_id IN (SELECT user_id FROM user_department WHERE department_id=?)
         AND user_id IN (SELECT user_id FROM user_company WHERE company_id=? AND is_enable=? ) ORDER BY id ASC LIMIT ?,?',[$company_id,$node_id,$company_id,$is_enable,$start,$page_size]);
        }
        DepartmentSingleResource::$users=collect($users);
        DepartmentSingleResource::$company_id=$company_id;//传递company_id
        $descendants=DepartmentSingleResource::collection($node)[0];
        return json_encode([
            'status'=>'success',
            'data'=>$descendants,
        ]);
    }
    /**
     * 后台管理系统-编辑部门(公司根部门除外)
     */
    public function editDepartment($data){
        try{
//            $parent_id = $data['parent_id'];
            $node_id = FunctionTool::decrypt_id($data['node_id']);
            $new_name = $data['name'];
            Department::where('id',$node_id)->update(['name'=>$new_name]);//编辑部门名称
//            $parent = Department::find($parent_id);
//            $node   = Department::find($node_id);
//            $parent->appendNode($node);//修改树节点
            $this->departmentRepository->saveInfo();//更新部门树缓存字段
            return ['status'=>'success','message'=>'编辑成功'];
        }catch (\Exception $e){
            return '服务器错误';
        }
    }

    /**
     * 后台管理系统-移除部门（公司根部门除外）
     * @param $data
     */
    public function deleteDepartment($data){
        try{
            $node_id = FunctionTool::decrypt_id($data['node_id']);
            $departments = $this->departmentRepository->getNodeChildrens($node_id);

            //递归移除部门和部门下的用户关联
            $this->removeDepartmentUsers($departments);

            //更新部门树缓存字段
            $this->departmentRepository->saveInfo();
            return ['status'=>'success','message'=>'删除成功'];
        }catch (\Exception $e){
            return ['status'=>'error','message'=>'删除失败'];
        }
    }


    /**
     * 递归移除部门中的所有用户
     * @param $departments 部门树
     */
    public  function removeDepartmentUsers($departments){
        if($departments){
            $count = self::getStaffNum($departments['id']);
            if($count){
                //移除部门下的用户关联
                Department::find($departments['id'])->users()->detach();
            }
            //删除部门信息
            Department::where('id',$departments['id'])->delete();
            if(!empty($departments['children'])){
                $this->removeDepartmentUsers($departments['children']);
            }
        }
    }
    /**
     * 获取节点下的员工数
     * @param $nodeId
     */
    public static function getStaffNum($node_id){
        $node = Department::find($node_id);
        $node_children_ids = $node->descendants()->pluck('id');
        $count = count($node->users);
        foreach ($node_children_ids as $children_id){
            $users = Department::find($children_id)->users;
            $count = $count + count($users);
        }
        return $count;
    }
    /**
     * 树内搜索
     * @param Request $request
     */
    public function searchTree(Request $request)
    {
        $user=auth('api')->user();
        if($user->current_company_id==0){
            return json_encode(['status'=>'fail','message'=>'没有加入公司']);
        }
        $company=Company::find($user->current_company_id);
        $name=$request->get('name',$company->name);
        $info=json_decode($company->department_json->info,true)['data'];
        $record=[
            'users'=>[],
            'node'=>[],
        ];//搜索记录数组搜出来的人员信息/非人员信息分开存储
        $index_str=[];//部门索引数组
        $index=[];//部门索引数组
        DepartmentTool::getDepartmentTool()->searchDataInTree($name,$info,$record,$index_str,$index);
        return json_encode([
            'status'=>'success',
            'data'=>$record,
        ]);
    }
    /**
     * 组织树内搜索人员/部门记录并返回相应的信息
     * @param $name:需要匹配的字符
     * @param $tree_data:树的json数据
     * @param array $record:搜索出的记录保存数组
     * @param string $index:树的父级节点id数组
     * @param string $index_str:树的父级节点name数组
     */
    public function searchDataInTree($name,$tree_data,array &$record,array &$index_str,array &$index)
    {
        //循环搜索
        foreach ($tree_data as $node){
            $index_str[]=$node['name'];//拼接索引
            $index[]=$node['id'];
            //先判断房前节点的名称是否和目标名称一样
            if(preg_match('/([\s\S]*'.$name.'[\s\S]*)/',$node['name'])){
                if(1){//如果不是外部联系人节点
                    $record['node'][]=[
                        'id'=>$node['id'],
                        'name'=>$node['name'],
                        'content'=>implode('|',$index_str),
                        'department_ids'=>$index,
                        'department_str'=>$index_str,
                    ];
                }else{

                }
            }
            //判断当前节点下的人员是否匹配
            $users=$node['users'];
            foreach ($users as $user){
                if(preg_match('/([\s\S]*'.$name.'[\s\S]*)/',$user->name)){
                    $record['user'][]=[
                        'id'=>$node['id'],
                        'name'=>$user->name,
                        'content'=>implode('|',$index_str),
                        'department_ids'=>$index,
                        'department_str'=>$index_str,
                    ];
                }
            }
            //循环子节点是否匹配
            $children=$node['children'];
            $c_count=count($children);
            if($c_count==0){
                $index=[];
                $index_str=[];
            }else{
                 $this->searchDataInTree($name,$children,$record,$index_str,$index);
            }
        }
    }
    /**
     * 初始化公司的组织结构树--用于公司创建的时候
     * @param $company_id:企业的id
     */
    public function initCompanyTree(Company $company)
    {
        $tree_json=BasicRepository::getBasicRepository()->getBasicData(config('basic.c_department_tree'))->body;//获取默认组织结构树的json
        $tree_data=json_decode($tree_json,true);//解析为数组
        //生成根节点
        $root=Department::create(['company_id'=>$company->id,'name'=>$company->name,'parent_id'=>null]);
        //拿到根节点
        $this->initCompanyTreeAddNode($company->id,$tree_data['children'],$root->id);//追加子节点
        //追加完之后修正树
        Department::fixTree();
        return $root;
    }
    /**
     * 生成默认的组织结构树,循环追加子节点
     * @param $company_id:目标企业id
     * @param $children:节点信息
     * @param $parent_node:父级节点信息
     */
    protected function initCompanyTreeAddNode($company_id,$children,$parent_id){
        if(!count($children)==0){
            //循环追加节点信息
            foreach ($children as $child){
                $node=Department::create(['company_id'=>$company_id,'name'=>$child['name'],'parent_id'=>$parent_id]);
                if(!count($child['children'])==0){
                    $this->initCompanyTreeAddNode($company_id,$child['children'],$node->id);//递归追加
                }
            }
        }
    }

    /**
     * 批量修改员工部门
     */
    public function batchEditDepartments($department_id,array $user_ids)
    {
        //获取公司所有部门id
        try{
            DB::beginTransaction();
            $department_id=FunctionTool::decrypt_id($department_id);
            $user_ids=FunctionTool::decrypt_id_array($user_ids);
            $user=auth('api')->user();
            $departments=DB::table('company_department')->where('company_id',$user->current_company_id)->pluck('id')->toArray();
            DB::table('user_department')->whereIn('department_id',$departments)->whereIn('user_id',$user_ids)->delete();
            $data=[];
            foreach ($user_ids as $v){
                $data[]=[
                    'department_id'=>$department_id,
                    'user_id'=>$v,
                ];
            }
            $update = DB::table('user_department')->insert($data);
            $this->departmentRepository->saveInfo();//更新部门树缓存字段
            DB::commit();
            return ['status'=>'success','message'=>'操作成功'];
        }catch (\Exception $exception){
            DB::rollBack();
            return ['status'=>'fail','message'=>'服务器错误'];
        }

    }
    /**
     * 批量修改员工职务
     */
    public function batchEditRoles($role_id,array $user_ids)
    {
        $user=auth('api')->user();
        $user_ids=FunctionTool::decrypt_id_array($user_ids);
        DB::table('company_user_role')->where('company_id',$user->current_company_id)->whereIn('user_id',$user_ids)->delete();
        $data=[];
        $role_id=FunctionTool::decrypt_id($role_id);
        foreach ($user_ids as $v){
            $data[]=[
                'role_id'=>$role_id,
                'company_id'=>$user->current_company_id,
                'user_id'=>$v,
            ];
        }
        $update=DB::table('company_user_role')->insert($data);
        if($update){
            return ['status'=>'success','message'=>'操作成功'];
        }else{
            return ['status'=>'fail','message'=>'操作失败'];
        }

    }
    /**
     * 批量停用员工
     */
    public function batchDisable(array $user_ids)
    {
        $user=auth('api')->user();
        $update=DB::table('user_company')->where('company_id',$user->current_company_id)->whereIn('user_id',$user_ids)->update(['is_enable'=>2]);
        if($update!=0){
            return ['status'=>'success','message'=>'操作成功'];
        }else{
            return ['status'=>'fail','message'=>'操作失败'];
        }
    }
    /**
     * 批量冻结员工
     */
    public function batchFreeze(array $user_ids)
    {
        $user=auth('api')->user();
        $user_ids=FunctionTool::decrypt_id_array($user_ids);
        $update=DB::table('user_company')->where('user_id','!=',$user->id)->where('company_id',$user->current_company_id)->whereIn('user_id',$user_ids)->update(['is_enable'=>0]);
        if($update!=0){
            return ['status'=>'success','message'=>'操作成功'];
        }else{
            return ['status'=>'fail','message'=>'操作失败'];
        }
    }

    /**
     * @param array $user_ids
     * @return array
     * 解冻
     */
    public function thaw(array $user_ids)
    {
        $user=auth('api')->user();
        $user_ids=FunctionTool::decrypt_id_array($user_ids);
        $update=DB::table('user_company')->where('company_id',$user->current_company_id)->whereIn('user_id',$user_ids)->update(['is_enable'=>1]);
        if($update!=0){
            return ['status'=>'success','message'=>'操作成功'];
        }else{
            return ['status'=>'fail','message'=>'操作失败'];
        }
    }
    /**
     * @param $tel
     * 手机号搜索查询用户
     */
    public function searchTel($tel)
    {
        $user_company_info=DB::table('user_company_info')->where('tel',$tel)->first();
        if($user_company_info){
            $user_company=UserCompany::where('user_id',$user_company_info->user_id)->where('company_id',$user_company_info->company_id)->first();
            $data=[
                'user_id'=>$user_company->user_id,
                'company_id'=>$user_company->company_id,
                'tel'=>$user_company->tel,
                'name'=>$user_company->name,
                'email'=>$user_company->email,
                'department_id'=>$user_company->department_id,
                'is_enable'=>$user_company_info->is_enable,
                'activation'=>$user_company_info->activation,
            ];
            return $data;
        }

    }

    /**
     * 指定部门下的用户
     * @param $department_id
     * @return \Illuminate\Support\Collection
     */
    public static function department_user($department_id)
    {
        $userIds=DB::table('user_department')->where('department_id',$department_id)->pluck('user_id');
        return $userIds;
    }

    public function searchUserByTel()
    {

    }
    /**
     * 测试方法
     * @param Request $request
     * @return array|float|int|string|void
     */
    public function test(Request $request)
    {
        return FunctionTool::encrypt_id($request['company_id']);
        return $this->departmentRepository->saveInfo();
        return $this->getCompanyAll($request);
        return $this->userDetail(['user_id'=>1,'company_id'=>1]);
//        dd(Company::find(1)->funs[0]->per_sort->pers[0]);

        $this->departmentRepository->saveInfo();
        return FunctionTool::decrypt_id($request->id);
//        $s=Role::find(54)->role_users->pluck('name','id');
//        $s=0x213;
//        dd($s);
        return $this->editDepartment(['name'=>'sss','node_id'=>FunctionTool::encrypt_id(2)]);
    }
}