<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;
use App\Http\Resources\company\CompanyPartnerRecordResource;
use App\Http\Resources\company\CompanyResource;
use App\Interfaces\CompanyInterface;
use App\Models\Company;
use App\Models\CompanyPartner;
use App\Models\CompanyPartnerRecord;
use App\Models\Permission;
use App\Models\PerSort;
use App\Models\User;
use App\Models\UserCompany;
use App\Repositories\BasicRepository;
use App\Repositories\CompanyPartnerRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * 公司or组织  工具类
 */
class CompanyTool implements CompanyInterface
{
    static private $companyTool;
    private $validateTool;//数据验证工具类
    private $companyRepository;//公司仓库类
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
        $this->validateTool=ValidateTool::getValidateTool();
        $this->companyRepository=CompanyRepository::getCompanyRepository();
    }
    /**
     * 单例模式
     */
    static public function getCompanyTool(){
        if(self::$companyTool instanceof self)
        {
            return self::$companyTool;
        }else{
            return self::$companyTool = new self;
        }
    }

    /**
     * 创建公司时一系列相关数据初始化操作
     * @param Request $request
     * @return string
     */
    public function generateCompany(Request $request): string
    {
        $user = auth('api')->user();//拿到访问用户
        DB::beginTransaction();
        try {
            //创建公司&授权基本权限
            $data = $request->all();
            $data['creator_id'] = $user->id;
            $data['sms_count'] = config('company.give_sms_count');//赠送短信默认条数
            $data['email_count'] = config('company.give_email_count');//赠送邮件默认条数
            $company = $this->companyRepository->createCompany($data);   //添加公司记录
            //创建公司数据限定记录(限定公司最大人数等等)
            $limit=config('company.basis_limit');
            $basis_limit=[];
            foreach ($limit as $v){
                $v['expire_date']=time()+3600*60;
                $v['company_id']=$company->id;
                $basis_limit[]=$v;
            }
            DB::table('company_basis_limit')->insert($basis_limit);
            $this->initCompanyInfo($company,$user);//初始化企业信息
            DB::commit();
            return json_encode(['status' => 'success', 'message' => '公司/组织创建成功']);
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return json_encode(['status' => 'fail', 'message' => '创建失败,服务器出错']);
        }
    }

    /**
     * 企业相关数据初始化
     * @param $company
     */
    public function initCompanyInfo($company,User $user): void
    {
//        $per_names = BasicRepository::getBasicRepository()->getBasicData(config('basic.c_permissions'))
//            ->load('permissions:name')->permissions->toArray();
//        $names = [];
//        foreach ($per_names as $v) {
//            $names[] = $v['name'];
//        }
        $per_names=Permission::where('per_sort_id','!=',0)->where('is_personal',0)->where('guard_name','gzt')->pluck('name')->toarray();
        $company->givePermissionTo($per_names);
        //创建者划入该公司
        $company->users()->attach($company->creator_id);
        //创建者信息单独保存一份在对应企业
        DB::table('user_company_info')->insert(['user_id'=>$user->id,'company_id'=>$company->id,'name'=>$user->name,'tel'=>$user->tel,'activation'=>1]);
        //生成默认的组织结构树并返回root_id
        $root=DepartmentTool::getDepartmentTool()->initCompanyTree($company);
        //创建者分到根节点下
        $root->users()->attach($company->creator_id);
        //写入公司拥有的共功模块(方便以后控制功能模块的启用情况)
        $perSorts=PerSort::all('id');
        $hasFun=[];
        foreach ($perSorts as $perSort){
            $hasFun[]=[
                'per_sort_id'=>$perSort->id,
                'company_id'=>$company->id,
            ];
        }
        DB::table('company_has_fun')->insert($hasFun);;
        //变更创建者的current_company_id
        UserRepository::getUserRepository()->updateUserData($user,['current_company_id'=>$company->id]);
        //复制角色信息
        RoleAndPerTool::copyBasicToCompany($company,$user);
        //网盘创建逻辑
        CompanyOssTool::getCompanyOssTool()->makeRootPath($company);
        //初始化企业公告栏目
        CompanyNoticeColumnTool::getCompanyNoticeColumnTool()->initCompanyColumn($company->id);
        //初始化企业评审通表单基础数据
        PstTool::getPstTool()->initCompanyFormBasicData($company->id);
        //初始化审批基础模板数据
        ApprovalTool::giveTemplateBasisData($company->id);
    }

    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 创建一个公司
     * @param \Illuminate\Http\Request $request
     */
    public function createCompany(Request $request)
    {
        $validator = $this->validateTool->sensitive_word_validate($request->all());//数据验证
        if (is_array($validator)) {
            return json_encode($validator);
        }
        return $this->generateCompany($request);
    }
    /**
     * 检查该名称是否已经有公司注册并认证
     * @param $name
     * @return string:false代表不存在已认证的企业反之亦然
     */
    public function checkNameVerified($name)
    {
        return $this->companyRepository->checkNameVerified($name)?
            json_encode(['status'=>false])
            :json_encode(['status'=>true]);
    }
    /**
     * 通过名称来搜索公司
     * @param $name
     */
    public function searchCompanyByName(Request $request)
    {
        $records=CompanyResource::collection($this->companyRepository->searchCompanyByName($request->get('name','探知')));
        return json_encode([
            'status'=>'success',
            'data'=>$records,
        ]);
    }
    /**
     * 校验某个企业是否存在
     * @param int $company_id
     */
    public function checkCompanyExist(int $company_id)
    {
        return $this->companyRepository->checkCompanyExist($company_id);
    }
    /**
     * 发送企业合作伙伴邀请
     * @param array $data=[
     *      comapny_id=>,
     *      invite_company_id=>,
     *      notification_way=>[],非必需
     * ]
     * @throws \ReflectionException
     */
    public function sendCompanyPartner(array $data)
    {
        $user=auth('api')->user();
        //验证权限
        if(!true){
            return json_encode(['status'=>'fail','message'=>'已发送邀请']);
        }
        //生成邀请记录
        $company_id=$data['invite_company_id'];//发起邀请的企业id
        $invite_company_id=$user->current_company_id;//被邀请企业的id
        $apply_description=$data['apply_description'];//申请描述
//        if($company_id==$invite_company_id){
//            return ['status'=>'fail','message'=>'操作失败,不能邀请自己公司'];
//        }
        $record=CompanyPartnerRecord::create([
            'company_id'=>$company_id,
            'operate_user_id'=>$user->id,
            'invite_company_id'=>$invite_company_id,
            'apply_description'=>$apply_description,
            'invite_company_name'=>Company::find($invite_company_id)->name,
        ]);
        //查询出被邀请企业的负责人
        $user_ids=RoleAndPerTool::get_company_target_per_users($company_id,['c_super_manage_per']);
        //目标人员推送数据
        $single_data = DynamicTool::getSingleListData(CompanyPartnerRecord::class, 1, 'company_id', 0,
            '通知:' . '企业邀请', Company::find($invite_company_id)->name.' 发起成为合作伙伴~', $record->created_at);
        NotifyTool::publishNotify($user_ids,$user->current_company_id, $record, ['need_notify'=>1], $single_data,[]);//此处方法顺序待调整
        return json_encode(['status'=>'success','message'=>'已发送邀请']);
    }
    /**
     * 处理企业合作伙伴的邀请
     * @param array $data=[
     *      'operate_type'=>agree 同意,refuse 拒绝
     *      'record_id'=>,记录id
     * ]
     */
    public function dealCompanyPartner(array $data)
    {
        try{
            DB::beginTransaction();
            $user=auth('api')->user();
            //验证权限
            if(!true){
                return json_encode(['status'=>'fail','message'=>'没有权限']);
            }
            //获取记录
            $record_id=$data['record_id'];
            $type_id=array_get($data,'type_id');
            $up_data=['state'=>$data['operate_type']=='agree'?1:0];
            $record=CompanyPartnerRecord::find($record_id);
            if($record->company_id==$record->invite_company_id){
                return ['status'=>'fail','message'=>'不能添加自己公司为合作伙伴'];
            }
            $ids=[$user->current_company_id,$record->invite_company_id];
            //验证是否已经是合作伙伴了
            $count=DB::table('company_partner')->whereIn('company_id',$ids)
                ->whereIn('invite_company_id',$ids)
                ->count();
            if($count!=0){
                return ['status'=>'fail','message'=>'已经是合作伙伴了'];
            }
            //提取发起企业需要通知的人员
            $user_ids=RoleAndPerTool::get_company_target_per_users($record->company_id,['c_super_manage_per']);
            if(CompanyPartnerRepository::updatePartnerRecord($record_id,$up_data)){
                //建立合作伙伴关系
                CompanyPartnerRepository::makePartnerRelation($record->company_id,$record->invite_company_id,$type_id);
                //进行通知反馈
            $single_data = DynamicTool::getSingleListData(CompanyPartnerRecord::class, 1, 'company_id', 0,
                '企业邀请', Company::find($record->invite_company_id)->name.
                ($data['operate_type']=='agree'?'同意':'拒绝').
                ' 成为合作伙伴~', $record->created_at);
            NotifyTool::publishNotify($user_ids,$record->company_id, $record, ['need_notify'=>1], $single_data,[]);//此处方法顺序待调整
            }
            DB::commit();
            return json_encode(['status'=>'success','message'=>'操作成功']);
        }catch (\Exception $exception){
            DB::rollBack();
            return json_encode(['status'=>'fail','message'=>'服务器错误!']);
        }

    }
    /**'
     * 获取某个企业的合作伙伴信息
     * @param $company_id:目标企业
     */
    public function getCompanyPartner($company_id)
    {
        $company_id=auth('api')->user()->current_company_id;
        //权限验证
        if(!true){

        }
        //获取企业id数组
        $ids=CompanyPartnerRepository::getCompanyPartnerIds($company_id);

        //调取企业信息
        $records=CompanyResource::collection($this->companyRepository->getCompanyByIds($ids));
        return json_encode([
            'status'=>'success',
            'data'=>$records
        ]);
    }
    /**
     * 获取某个企业的合作伙伴的邀请记录
     * @param int $company_id
     */
    public function getCompanyPartnerRecord(int $company_id)
    {
        $company_id=auth('api')->user()->current_company_id;
        //权限验证
        if(!true){

        }
        //调取企业合作伙伴邀请记录
        $records=CompanyPartnerRecordResource::collection(CompanyPartnerRepository::getCompanyPartnerRecord($company_id));
        return json_encode([
            'status'=>'success',
            'data'=>$records,
        ]);
    }
    /**
     * 获取公司列表
     */
    public function getCompanyList($current_company_id=null)
    {
        $user=auth('api')->user();
        $company_id=UserCompany::where('user_id',$user->id)->where('is_enable',1)->where('activation',1)->pluck('company_id')->toarray();
        $relate_companys=Company::find($company_id)->map(function ($company){
            return [
                'id'=>FunctionTool::encrypt_id($company->id),
                'name'=>$company->name,
            ];
        });
        $current_company_id=$current_company_id!==null?$current_company_id:$user->current_company_id;
        $current_company=Company::find($current_company_id);
        if($current_company){
            $current_company=[
            'id'=>FunctionTool::encrypt_id($current_company->id),
            'name'=>$current_company->name
            ];
        }else{
            $current_company=[];
        }
        return ['current_company'=>$current_company,'relate_companys'=>$relate_companys];
    }
    /**
     * 用户切换公司
     */
    public function changeCompany($company_id)
    {
        $user=auth('api')->user();
        if($company_id!=0){
            $company_id=FunctionTool::decrypt_id($company_id);
        }
        $update=0;
        if(gettype($company_id)=='integer'){
            $update=User::find($user->id)->update(['current_company_id'=>$company_id]);
        }
        $data=$this->getCompanyList();
        if($update==0){
            return ['status'=>'fail','message'=>'切换失败,请重新检查','data'=>$data];
        }
        return ['status'=>'success','message'=>'切换成功','data'=>$data];
    }

}