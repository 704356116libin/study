<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;
use App\Http\Resources\FileResource;
use App\Http\Resources\notice\CompanyNoticeDetailResource;
use App\Http\Resources\notice\CompanyNoticeListResource;
use App\Http\Resources\user\UserCompanyCardResource;
use App\Http\Resources\user\UserSimpleResource;
use App\Interfaces\CompanyNoticeInterface;
use App\Models\Company;
use App\Models\CompanyNotice;
use App\Models\CompanyNoticeColumn;
use App\Models\CompanyPartner;
use App\Models\FileUseRecord;
use App\Models\PersonalOssFile;
use App\Models\User;
use App\Models\UserCompany;
use App\Repositories\CompanyDepartmentRepository;
use App\Repositories\CompanyNoticeRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\OssFile;
use OSS\OssClient;

/**
 * 企业公告工具类
 */
class CompanyNoticeTool implements CompanyNoticeInterface
{
    static private $companyNoticeTool;
    private $functionTool;//公共方法工具类
    private $departmentTool;//部门工具类
    private $companyTool;//企业工具类
    private $companyOssTool;//企业云存储工具类
    private $companyNoticeBrowseTool;//企业公告浏览记录工具类
    private $companyNoticeRepository;//企业公告仓库
    private $companyDepartmentRepository;//企业部门仓库类
    private $userRepository;//用户仓库类
    private $validateTool;//数据验证工具类
    private $personalOssTool; //个人云存储工具类
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
        $this->functionTool=FunctionTool::getFunctionTool();
        $this->companyNoticeRepository=CompanyNoticeRepository::getCompanyNoticeRepository();
        $this->departmentTool=DepartmentTool::getDepartmentTool();
        $this->companyOssTool=CompanyOssTool::getCompanyOssTool();
        $this->companyTool=CompanyTool::getCompanyTool();
        $this->companyNoticeBrowseTool=CompanyNoticeBrowseTool::getCompanyNoticeBrowseTool();
        $this->companyDepartmentRepository=CompanyDepartmentRepository::getDepartmentRepository();
        $this->userRepository=UserRepository::getUserRepository();
        $this->validateTool=ValidateTool::getValidateTool();
        $this->personalOssTool =PersonalOssTool::getPersonalOssTool();
    }
    /**
     * 单例模式
     */
    static public function getCompanyNoticeTool(){
        if(self::$companyNoticeTool instanceof self)
        {
            return self::$companyNoticeTool;
        }else{
            return self::$companyNoticeTool = new self;
        }
    }
    /**
     * @param $data
     * @param $company_id
     * @param $user_ids
     * @param $user
     * @param $guard_json
     * @return array
     */
    protected function makeUserIds($data, $company_id, $user_ids, $user, $guard_json): array
    {
//        if ($data['guard_json'] == 'all') {
//            $allow_user = $data['guard_json'];
//            $department_ids = $this->companyDepartmentRepository->getCompanyDepartmentIds($company_id);
//            $user_ids = array_values(array_unique(
//                array_merge(
//                    $this->departmentTool->getNodesUsers([1, 2, 3, 4, 5])
//                    , $user_ids
//                    , [$user->id])));
//        } else {
//            $department_ids = $guard_json['organizational'];
//            $user_ids = FunctionTool::decrypt_id_array($guard_json['user_ids']);
//            $user_ids = array_values(array_unique(
//                array_merge(
//                    $this->departmentTool->getNodesUsers([1, 2, 3, 4, 5])
//                    , $user_ids
//                    , [$user->id])));
//        }
        $user_id=[];
        $partner_company_ids=[];
        $wai_ids=[];
        foreach ($guard_json['organizational'] as $value){
            $user_id[]=$value['key'];
        }
        foreach ($guard_json['partner'] as $value){
            $partner_company_ids[]=$value['key'];
        }
        foreach ($guard_json['externalContact'] as $value){
            $wai_ids[]=$value['key'];
        }
        return ['user_ids'=>$user_id,'partner_company_ids'=>$partner_company_ids,'wai_ids'=>$wai_ids];
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 创建一个公告
     * @param $company_id:所选公司的id
     * @param $data:公告的数据
     * @param $user:访问用户
     * @param $allow_user:授权查看的人员ids
     * @return mixed
     */
    public function makeNotice($company_id, $data, $user, $allow_user)
    {
        $c_notice_column_id=FunctionTool::decrypt_id($data['c_notice_column_id']);
        return CompanyNotice::create([
            'company_id' => $company_id,
            'title' => $data['title'],
            'content' => $data['content'],
            'type' => CompanyNoticeColumn::find($c_notice_column_id)->name,
            'is_show' => $data['operate_type'] == 'publish' ? 1 : 0,//需要传入是保存还是直接发布的标识
            'is_draft' => $data['operate_type'] == 'publish' ? 0 : 1,//判断是否是保存为草稿
            'organiser' => $user->name,//发布公告的用户在该公司的名字
            'c_notice_column_id' => $c_notice_column_id,
            'allow_user' => $allow_user,
            'allow_download' =>array_get($data,'allow_download',0) ,
            'guard_json' => $data['guard_json'],
        ]);
    }
    /**
     * 新建公告
     * @param array $data:公告数据
     */
    public function add(Request $request)
    {
        $files=$_FILES;
        $user=auth('api')->user();
        $data=$request->all();//取出前端传递的公告数据
        $company_id=$user->current_company_id;//当前企业id
        /**
         * 验证是否有公告编辑权
         */
        if(!true){
            return json_encode(['status'=>'fail','message'=>'您没有相应权限无法操作!']);
        }
        //数据验证--title
        $validator = $this->validateTool->sensitive_word_validate(['name'=>$request->title]);
        if (is_array($validator)) {
            $validator['index']='title';
            return json_encode($validator);
        }
        //数据验证--content
        $validator = $this->validateTool->sensitive_word_validate(['name'=>$request->get('content')]);
        if (is_array($validator)) {
            $validator['index']='content';
            return json_encode($validator);
        }
        //验证企业是否存在
        if(!$this->companyTool->checkCompanyExist($company_id)){
            return json_encode(['status'=>'fail','message'=>'所在企业不存在/已注销']);
        }
        //计算企业云存储的剩余空间是否满足附件的大小&文件合法性校验
        if(!count($files)==0){
            $message=$this->companyOssTool->ossSizeIsEnough($company_id,$files);
            if(count($message)!=0){
                return json_encode(['status'=>'fail','message'=>implode(',',$message)]);
            }
        }
        //组装允许查看人员的json(牵扯到外部人员,以及all判断)
        $partner_company_ids=[];
        $wai_ids=[];
        $user_ids=[];
        $allow_user='all';
        $guard_json=$data['guard_json'] == 'all'?$data['guard_json']:json_decode($data['guard_json'],true);
        if($guard_json!='all'){
            $ids = $this->makeUserIds($data, $company_id, $user_ids, $user, $guard_json['checkedPersonnels']);
            $allow_user=json_encode(['company_u_ids'=>$ids['user_ids'],'partner_company_ids'=>$ids['partner_company_ids'],'wai_ids'=>$ids['wai_ids']]);//组装可见人id  json
            $a=FunctionTool::decrypt_id_array($ids['user_ids']);
//            $b=FunctionTool::decrypt_id_array($ids['partner_company_ids']);//合作伙伴公司id
            $c=FunctionTool::decrypt_id_array($ids['wai_ids']);
            $user_ids=array_merge($a,$c);
        }else{
            $user_ids=UserCompany::where('company_id',$company_id)
                ->where('is_enable',1)
                ->where('activation',1)
                ->pluck('user_id')->toarray();
        }
        //创建公告
        $notice= $this->makeNotice($company_id, $data, $user, $allow_user);
        $company=Company::find($company_id);
        //公告通知相关人员
        try {
            //若是直接发布的状态则进行通知逻辑
            if ($data['operate_type'] == 'publish') {
                //组装动态列表单个数据格式
                $single_data = DynamicTool::getSingleListData(CompanyNotice::class, 1, 'company_id', $company->id,
                    '工作通知:' . $company->name, $notice->title, $notice->created_at);
                NotifyTool::publishNotify($user_ids, $user->current_company_id, $notice, json_decode($request->notification_way,true), $single_data,[]);//此处方法顺序待调整
                $this->companyNoticeRepository->updateNotice($notice->id, ['notified' => 1]);//更新公告通知状态
            }
        } catch (\Exception $e) {
            dump($e);
        }
        //公告的附件处理(文件上传)
        if(count($files)==0){
            return json_encode(['status'=>'success','message'=>'添加成功']);
        }else{
            $data= $this->companyOssTool->uploadFile($files,[
                'oss_path'=>$company->oss->root_path.'公告附件',//公告上传的云路径,其他模块与之类似
                'model_id'=>$notice->id,//关联模型的id
                'model_type'=>CompanyNotice::class,//关联模型的类名
                'company_id'=>$company_id,//所属公司的id
                'uploader_id'=>$user->id,//上传者的id
                ]);
            if($data===true){
                return json_encode(['status'=>'success','message'=>'添加成功']);
            }else{
                return json_encode(['status'=>'fail','message'=>'添加成功,但'.$data]);
            }
        }
    }
    /**
     * 移除公告
     * @param array $data
     */
    public function remove($notice_id)
    {
        //先判断权限
        if(!is_array($notice_id)){
            $notice_id=$this->functionTool->decrypt_id($notice_id);//解码关键id
            //移除公告
            CompanyNotice::find($notice_id)->delete();
            //移除公告浏览记录
            $this->companyNoticeRepository->removeNoticeBrowseRecord($notice_id);
            //移除用户关注的
            $this->companyNoticeRepository->removeNoticeFollowRecord($notice_id);
            return json_encode(['status'=>'success','message'=>'删除成功']);
        }else{
            $notice_id=$this->functionTool->decrypt_id($notice_id);//解码关键id
            //移除公告
            CompanyNotice::whereIn($notice_id)->delete();
            //移除公告浏览记录
            //移除用户关注的
            return json_encode(['status'=>'success','message'=>'删除成功']);
        }
    }
    /**
     * 置顶公告
     * @param array $data
     */
    public function topRemark($notice_id)
    {
        if(is_null($notice_id)){
            return;
        }
        CompanyNotice::where('id',FunctionTool::decrypt_id($notice_id))->update(['is_top'=>1]);
        return json_encode(['status'=>'success','message'=>'置顶成功']);
    }
    /**
     * 取消置顶公告
     * @param array $data
     */
    public function topCancle($notice_id)
    {
        CompanyNotice::where('id',FunctionTool::decrypt_id($notice_id))->update(['is_top'=>0]);
        return json_encode(['status'=>'success','message'=>'取消置顶成功']);
    }
    /**
     * 返回某用户所有有权看到的公告(应用Laravel资源类)
     */
    public function getShowNotice($data,User $user)
    {
        $top=$this->companyNoticeRepository->getAllShow($user->current_company_id,$user);
        $now_page=array_get($data,'now_page',1);//现在页码
        $page_size=array_get($data,'page_size',10);//每页条数
        $notices=CompanyNoticeListResource::collection(collect(array_slice($top->toArray(),($now_page-1)*$page_size,$page_size)));

        return json_encode([
            'status'=>'success',
            'page_count'=>ceil(count($top)/$page_size),
            'page_size'=>$page_size,
            'now_page'=>$now_page,
            'all_count'=>count($top),
            'data'=>$notices,
        ]);
    }
    /**
     * 返回某用户所有有权看到的公告--分栏目(应用Laravel资源类)
     */
    public function getShowNoticeByColumn($data,User $user)
    {
        $data['company_id']=$user->current_company_id;
        $top=$this->companyNoticeRepository->getAllShowByColumn($data,$user);
        $now_page=array_get($data,'now_page',1);//现在页码
        $page_size=array_get($data,'page_size',10);//每页条数
        $notices=CompanyNoticeListResource::collection(collect(array_slice($top->toArray(),($now_page-1)*$page_size,$page_size)));

        return json_encode([
            'status'=>'success',
            'page_count'=>ceil(count($top)/$page_size),
            'page_size'=>$page_size,
            'now_page'=>$now_page,
            'all_count'=>count($top),
            'data'=>$notices,
        ]);
    }
    /**
     * 返回某用户所有有权看到的公告--搜索标题(应用Laravel资源类)
     */
    public function searchNoticeByTitle(array $data,User $user)
    {
        $top=$this->companyNoticeRepository->searchNoticeByTitle($data,$user);
        $now_page=array_get($data,'now_page',1);//现在页码
        $page_size=array_get($data,'page_size',10);//每页条数
        $notices=CompanyNoticeListResource::collection(collect(array_slice($top->toArray(),($now_page-1)*$page_size,$page_size)));

        return json_encode([
            'status'=>'success',
            'page_count'=>ceil(count($top)/$page_size),
            'page_size'=>$page_size,
            'now_page'=>$now_page,
            'all_count'=>count($top),
            'data'=>$notices,
        ]);
    }
    /**
     * 获取某条公告的浏览记录(已浏览的)
     * @param $notice_id
     * @param $type:操作
     */
    public function getNoticeLookRecord($notice_id,string $type,Request $request=null)
    {
        $user=auth('api')->user();
        $notice_id=FunctionTool::decrypt_id($notice_id);//拿到目标公告id
        //获取目标公告记录
        $record=CompanyNotice::find($notice_id);
        if(is_null($record)){
            return json_encode(['status'=>'fail','message'=>'目标公告不存在']);
        }
        //获取已浏览的人员id&user
        $browse_ids=[];
        $now_page=1;//当前页默认值
        if($request!=null){
            $now_page=$request->get('now_page',1);
            $page_size=20;//每页个数
            $browse_ids=$this->companyNoticeRepository->getNoticeBrowseUserIds($notice_id,($now_page-1)*$page_size,$page_size);
        }else{
            $browse_ids=$this->companyNoticeRepository->getNoticeBrowseUserIds($notice_id,0,0,'all');
        }
        if($type=='get_browse_ids'){
            return $browse_ids;
        }else{
            UserSimpleResource::$company_id=$user->current_company_id;
            $browse_user=UserSimpleResource::collection($this->userRepository->getUser($browse_ids['records']));
            return json_encode([
                'status'=>'success',
                'count'=>$browse_ids['count'],
                'now_page'=>$now_page,
                'data'=>$browse_user
            ]);
        }
    }
    /**
     * 获取某条公告的浏览记录(未浏览的)
     * @param $notice_id
     */
    public function getNoticeUnLookRecord(Request $request)
    {
        $user=auth('api')->user();
        $en_id=$request->notice_id;
        $notice_id=FunctionTool::decrypt_id($en_id);//拿到目标公告id
        //获取目标公告记录
        $record=CompanyNotice::find($notice_id);
        if(is_null($record)){
            return json_encode(['status'=>'fail','message'=>'目标公告不存在']);
        }
        $browse_ids=$this->getNoticeLookRecord($en_id,'get_browse_ids');//获取已浏览人的ids
        if(!is_array($browse_ids)){
            return json_encode(['status'=>'fail','message'=>'出错了']);
        }
        $all=DB::table('user_company')
            ->where('company_id',$record->company_id)
            ->where('activation',1)
            ->where('is_enable',1)
            ->pluck('user_id')->toArray();
        $allow_user=$record->allow_user==='all'?$all:json_decode($record->allow_user,true)['company_u_ids'];//获取可见人数组
        //提取未浏览人员的id&user
        $un_browse_ids=[];
        foreach ($allow_user as $item){
            if(!in_array($item,$browse_ids))$un_browse_ids[]=$item;
        }
        //分页加载
        $now_page=$request->get('now_page',1);
        $page_size=20;
        $un_browse_ids=array_slice($un_browse_ids,($now_page-1)*$page_size,$page_size);
        //设置公司id
        UserSimpleResource::$company_id=$user->current_company_id;
        $un_browse_user=UserSimpleResource::collection($this->userRepository->getUser($un_browse_ids));
        return [
            'status'=>'success',
            'data'=>$un_browse_user,
        ];
    }
    /**
     * 添加某条公告的浏览记录
     */
    public function addNoticeLookRecord(array $data)
    {
        try {
            DB::table('company_notice_look_record')
                ->insert($data);
            return json_encode(['status'=>'success','message'=>'浏览记录加入成功']);
        } catch (\Exception $e) {
            return json_encode(['status'=>'fail','message'=>'浏览记录加入不成功']);
        }
    }
    /**
     * 用户关注某条公告
     * @param $notice_id
     */
    public function userFollowNotice($notice_id)
    {
        $user=auth('api')->user();
        if ($this->checkUserFollow(FunctionTool::decrypt_id($notice_id))){
            return json_encode(['status'=>'fail','message'=>'您已经关注过了']);
        }
        try {
            if (is_null(CompanyNotice::find(FunctionTool::decrypt_id($notice_id))))
                return json_encode(['status'=>'fail','message'=>'公告不存在']);
            $user->follow_notices()->save(CompanyNotice::find(FunctionTool::decrypt_id($notice_id)));
            return json_encode(['status'=>'success','message'=>'关注成功']);
        } catch (\Exception $e) {
            return json_encode(['status'=>'fail','message'=>'出错了']);
        }
    }
    /**
     * 用户取关注某条公告
     * @param $notice_id
     */
    public function userDeFollowNotice($notice_id)
    {
        $user=auth('api')->user();
        if (!$this->checkUserFollow(FunctionTool::decrypt_id($notice_id))){
            return json_encode(['status'=>'fail','message'=>'您没关注过该公告']);
        }
        try {
            DB::table('user_notice_follow')
                ->where('notice_id',FunctionTool::decrypt_id($notice_id))
                ->where('user_id',$user->id)
                ->delete();
            return json_encode(['status'=>'success','message'=>'取消关注成功']);
        } catch (\Exception $e) {
            return json_encode(['status'=>'fail','message'=>'出错了']);
        }
    }
    /**
     * 拿到某用户在某公司关注的公告列表--分页
     */
    public function getUserFollowNoticeList(Request $request){
        $data=$request->all();
        $user=auth('api')->user();
        $now_page=array_get($data,'now_page',1);//现在页码
        $page_size=array_get($data,'page_size',10);//每页条数
        $data=$this->companyNoticeRepository->getUserFollowNoticeList($user->current_company_id,$user->id,($now_page-1)*$page_size,$page_size);
        $notices=CompanyNoticeListResource::collection($data['records']);
        return json_encode([
            'status'=>'success',
            'page_count'=>ceil($data['count']/$page_size),
            'page_size'=>$page_size,
            'now_page'=>$now_page,
            'all_count'=>$data['count'],
            'data'=>$notices,
        ]);
    }
    /**
     * 检查用户是否关注某个公告
     * @param User $user
     */
    public function checkUserFollow($notice_id)
    {
        $user=auth('api')->user();
        $record=DB::table('user_notice_follow')
                    ->where('user_id',$user->id)
                    ->where('notice_id',$notice_id)
                    ->get();
        if(count($record)==0){
            return false;
        }else{
            return true;
        }
    }
    /**
     * 通过id获取公告--获取单挑公告的详情
     * @param $notice_id
     */
    public function getNoticeById($notice_id)
    {
        $notice_id=FunctionTool::decrypt_id($notice_id);
        $user=auth('api')->user();
        if(is_null($notice_id)){
            return json_encode(['status'=>'fail','message'=>'缺少参数id']);
        }
        $notice=CompanyNotice::find($notice_id);
        if(is_null($notice)){
            return json_encode(['status'=>'fail','message'=>'查找的公告不存在']);
        }
        //添加公告浏览次数的处理
        $this->companyNoticeRepository->updateNotice($notice->id,['browse_count'=>$notice->browse_count+1]);//浏览次数自增
        //公告-用户浏览记录添加
        if(!$this->companyNoticeBrowseTool->checkUserRecordExist($user->id,$notice_id)){
            $this->companyNoticeBrowseTool->addUserRecord(['user_id'=>$user->id,'notice_id'=>$notice_id,
                                                    'time'=>date('Y-m-d H:i:s',time()),'info'=>'data']);//添加浏览记录
        }
        return json_encode(['status'=>'success','notice'=>new CompanyNoticeDetailResource($notice),'files'=>FileResource::collection($notice->files)]);
    }
    /**
     * 撤销某个公告
     * @param $notice_id
     */
    public function cancelNotice($notice_id)
    {
        $this->companyNoticeRepository->cancelNotice(FunctionTool::decrypt_id($notice_id));
        return json_encode(['status'=>'success','message'=>'撤销成功']);
    }
    /**
     * 获取所有撤销(未展示的公告)
     * @param $company_id
     */
    public function getCancelNotice(array $data)
    {
        /**
         * 此处需要接入权限过滤--编辑权只能看到自己发布的草稿公告,公告管理权的可以看到所有的公告,
         */
        $user=auth('api')->user();
        if(!true){
            json_encode(['status'=>'faile','message'=>'权限不足']);
        }
        $notices= $this->companyNoticeRepository->getDraftNotice($user->current_company_id);
        $now_page=array_get($data,'now_page',1);//现在页码
        $page_size=array_get($data,'page_size',10);//每页条数
        $data=CompanyNoticeListResource::collection(collect(array_slice($notices->toArray(),($now_page-1)*$page_size,$page_size)));
        return json_encode([
            'status'=>'success',
            'page_count'=>ceil(count($notices)/$page_size),
            'page_size'=>$page_size,
            'now_page'=>$now_page,
            'all_count'=>count($notices),
            'data'=>$data,
        ]);
    }
    /**
     * 更新某个公告信息
     * @param $company_id
     */
    public function updateNotice(Request $request)
    {
        $files=$_FILES;
        $user=auth('api')->user();
        $data=$request->all();
        $company_id=$user->current_company_id;//当前企业id
//        //数据验证--title
        $validator = $this->validateTool->sensitive_word_validate(['name'=>$request->title]);
        if (is_array($validator)) {
            $validator['index']='title';
            return json_encode($validator);
        }
//        //数据验证--content
        $validator = $this->validateTool->sensitive_word_validate(['name'=>$request->get('content')]);
        if (is_array($validator)) {
            $validator['index']='content';
            return json_encode($validator);
        }
        //组装可见人数组
        $user_ids=[];
        $allow_user='all';
        $guard_json=(!isset($data['guard_json']))?($data['guard_json'] == 'all'?$data['guard_json']:json_decode($data['guard_json'],true)):'all';
        if($guard_json != 'all') {
            $ids = $this->makeUserIds($data, $company_id, $user_ids, $user, $guard_json['checkedPersonnels']);
            $allow_user=json_encode(['company_u_ids'=>$ids['user_ids'],'partner_company_ids'=>$ids['partner_company_ids'],'wai_ids'=>$ids['wai_ids']]);//组装可见人id
        }
       
        //组装更新数据
        $c_notice_column_id=FunctionTool::decrypt_id($data['c_notice_column_id']);
        $notice_data=[
           'title' => $data['title'],
           'content' => $data['content'],
           'type' => CompanyNoticeColumn::find($c_notice_column_id)->name,
           'c_notice_column_id' => $c_notice_column_id,
           'allow_user' => $allow_user,//这个参数需要单独处理
           'allow_download' =>array_get($data,'allow_download',0) ,
           'guard_json' => $data['guard_json'],];
        $notice_id=FunctionTool::decrypt_id($data['notice_id']);
        //更新公告的基础信息
        $this->companyNoticeRepository->updateNotice($notice_id,$notice_data);
        $company=Company::find($company_id);
        $notice=CompanyNotice::find($notice_id);//拿到更新的公告

        //移除公告的附件
        $filesId = FunctionTool::decrypt_id_array(json_decode($data['deletefilesId']));
        foreach ($filesId as $fileid){
            CompanyOssTool::deleteFile($fileid);
        }

        //是否更新附件的逻辑处理
        if(count($files)==0){
            return json_encode(['status'=>'success','message'=>'更改成功']);
        }else{
            $data= CompanyOssTool::uploadFile($files,[
                'oss_path'=>$company->oss->root_path.'公告附件',//公告上传的云路径,其他模块与之类似
                'model_id'=>$notice->id,//关联模型的id
                'model_type'=>CompanyNotice::class,//关联模型的类名
                'company_id'=>$company->id,//所属公司的id
                'uploader_id'=>$user->id,//上传者的id
            ]);
            if($data){
                return json_encode(['status'=>'success','message'=>'添加成功']);
            }else{
                return json_encode(['status'=>'fail','message'=>$data]);
            }
        }

    }
    /**
     * 重新发布某条公告
     * @param $notice_id
     */
    public function publish(Request $request)
    {
        $user=auth('api')->user();
        $notice=CompanyNotice::find(FunctionTool::decrypt_id($request->notice_id));
        if($notice->is_show==1){
            return json_encode(['status'=>'fail','message'=>'该公告已经处于发布状态']);
        }
        $this->companyNoticeRepository->updateNotice($notice->id,['is_show'=>1,'is_draft'=>0,'updated_at'=>date('Y-m-d H:i:s',time())]);
        try {
            //若是直接发布的状态则进行通知逻辑
                //组装动态列表单个数据格式
                $single_data = DynamicTool::getSingleListData(CompanyNotice::class, 1, 'company_id', $notice->company_id,
                    '工作通知:' . Company::find($notice->company_id)->name,$notice->notified==1?'公告更新了:'.$notice->title:$notice->title, $notice->created_at);
//                $user_ids=json_decode($notice->allow_user,true)['company_u_ids'];
            $allow_user=$notice->allow_user==='all'?'all':json_decode($notice->allow_user,true);
            if($allow_user!='all'){
                $a=FunctionTool::decrypt_id_array($allow_user['company_u_ids']);
//            $b=FunctionTool::decrypt_id_array($ids['partner_company_ids']);//合作伙伴公司id
                $c=FunctionTool::decrypt_id_array($allow_user['wai_ids']);
                $user_ids=array_merge($a,$c);
            }else{
                $user_ids=UserCompany::where('company_id',$notice->company_id)
                    ->where('is_enable',1)
                    ->where('activation',1)
                    ->pluck('user_id')
                    ->toarray();
            }
                NotifyTool::publishNotify($user_ids, $user->current_company_id, $notice, is_null($request->notification_way)?['need_notify'=>1]:$request->notification_way,
                    $single_data,[]);
                $this->companyNoticeRepository->updateNotice($notice->id, ['notified' => 1]);//更新公告通知状态
        } catch (\Exception $e) {
            dump($e);
        }
        return json_encode(['status'=>'success','message'=>'发布成功']);
    }
    /**
     * 获取我的外部公告(合作伙伴)
     */
    public function getPartnerNotice($data)
    {
        $user=auth('api')->user();
        $company_id=$user->current_company_id;
        //判断我在此公司是否有外圈公告接收权限
        $pers=$this->partnerVisible($user);
        if(!$pers){
            return ['status'=>'fail','message'=>'没有外部公告可见权限'];
        }
        //外圈公告
        $top=$this->getAllAllNotice($user);
        //合作伙伴公告
        $top=$top->filter(function ($notice) use ($company_id){
                return in_array($company_id,json_decode($notice->allow_user,true)['partner_company_ids']);//判断某个值是否在某数组中
            });
        $now_page=array_get($data,'now_page',1);//现在页码
        $page_size=array_get($data,'page_size',10);//每页条数
        $notices=CompanyNoticeListResource::collection(collect(array_slice($top->toArray(),($now_page-1)*$page_size,$page_size)));

        return json_encode([
            'status'=>'success',
            'page_count'=>ceil(count($top)/$page_size),
            'page_size'=>$page_size,
            'now_page'=>$now_page,
            'all_count'=>count($top),
            'data'=>$notices,
        ]);
    }
    /**
     * 获取我的外部公告(合作伙伴)
     */
    public function getExternalNotice($data)
    {
        $user=auth('api')->user();
        $user_id=$user->id;
        //外圈公告
        $top=$this->getAllAllNotice($user);
        //合作伙伴公告
        $top=$top->filter(function ($notice) use ($user_id){
            return false;
            return in_array($user_id,json_decode($notice->allow_user,true)['wai_ids']);//判断某个值是否在某数组中
        });
        $now_page=array_get($data,'now_page',1);//现在页码
        $page_size=array_get($data,'page_size',10);//每页条数
        $notices=CompanyNoticeListResource::collection(collect(array_slice($top->toArray(),($now_page-1)*$page_size,$page_size)));

        return json_encode([
            'status'=>'success',
            'page_count'=>ceil(count($top)/$page_size),
            'page_size'=>$page_size,
            'now_page'=>$now_page,
            'all_count'=>count($top),
            'data'=>$notices,
        ]);
    }
    /**
     * 外部公告
     */
    public function getAllAllNotice($user)
    {
        $company_id=$user->current_company_id;
        //$partners 合作伙伴公司ids
        $partners=DB::table('company_partner')->where('company_id',$company_id)->orWhere('invite_company_id',$company_id);//合作伙伴id
        $partners=array_unique(array_merge($partners->pluck('company_id')->toArray(),$partners->pluck('invite_company_id')->toArray()));
        //外圈公告
        return $this->companyNoticeRepository->allShowNotice($partners);
    }
    /**
     * 公告合作伙伴可见人id(外部公告可见权限的人可见)
     */
    public function partnerVisible($user)
    {
        $pers=RoleAndPerTool::get_user_c_per($user->id,$user->current_company_id);//外部公告接收权限
        if(in_array('c_external_notice_receive',$pers)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 下载附件（临时方法）
     * @param Request $request
     */
    public function downloadFile($data){
        $user = auth('api')->user();
        $fileid = FunctionTool::decrypt_id($data['id']);
        return $this->personalOssTool->singleFileUpload([$fileid],'company',$user->current_company_id);
//        $file = OssFile::find($fileid);
//        //添加下载记录
//        FileUseRecord::create([
//            'name'=>$file->name,
//            'type'=>'下载',
//            'company_id'=>$user->current_company_id,
//            'user_id'=>$user->id,
//            'path'=>$file->oss_path,
//        ]);
//        //下载文件开始
//        // 指定文件下载路径。
//        $localfile = $user->id . $file->name;
//        $options = array(
//            OssClient::OSS_FILE_DOWNLOAD => $localfile
//        );
//        //获取文件类型
//        $explode = explode('.', $file->oss_path);
//        $type = $explode[count($explode) - 1];
//
//        $ossClient = new OssClient(env('Aliyun_OSS_AccessKeyId'), env('Aliyun_OSS_AccessKeySecret'), env('Aliyun_OSS_endpoint'));
//        $ossClient->getObject('gzts', $file->oss_path, $options);
//        //将文件返回客户端下载
//        header("Content-Type: application/zip");
//        header("Content-Transfer-Encoding: Binary");
//        header("Content-Length: " . filesize($localfile));
//        header("filename:" . $file->name);
//        readfile($localfile);
//        //删除本地文件
//        unlink($localfile);
//        exit();
    }

    /**
     * 转存附件（存网盘）
     * @param Request $request
     */
    public function transferFile($data){
        $user = auth('api')->user();
        $fileid = FunctionTool::decrypt_id($data['id']);
        $file = OssFile::find($fileid);
        try{
            DB::beginTransaction();
            //检测个人存储空间
            $message = $this->personalOssTool->ossSizeIsEnough($user->id, [0 => ['name' => $file->name, 'size' => $file->size]]);
            if (count($message) != 0) {
                return json_encode(['status' => 'fail', 'message' => implode(',', $message)]);
            }
            //截取文件的扩展名--在$matches[1]中
            preg_match(config('regex.file_extension'), $file->name, $matches);
            //复制文件至指定路径
            $to_path = User::find($user->id)->oss->root_path . $data['target_directory'] .
                FunctionTool::encrypt_id($file->id) . str_random(8) . '.' . $matches[1];
            //拷贝文件（从企业网盘到个人网盘）
            Storage::disk('oss')->copy($file->oss_path, $to_path);
            //创建新的文件
            PersonalOssFile::create([
                'uploader_id' => $user->id,
                'user_id' => $user->id,
                'size' => $file->size,
                'name' => $file->name,
                'oss_path' => $to_path,//新文件路径
            ]);
            //更新个人云oss空间
            $this->personalOssTool->updateNowSize($user->id, $file->size, 'add');
            //添加存网盘记录
            FileUseRecord::create([
                'name'=>$file->name,
                'type'=>'存网盘',
                'company_id'=>$user->current_company_id,
                'user_id'=>$user->id,
                'path'=>$file->oss_path,
            ]);
            return json_encode(['status' => 'success', 'message' => '文件转存成功']);
        }catch (\Exception $e){
            DB::rollBack();
            return json_encode(['status' => 'error', 'message' => '文件转存失败']);
        }
    }

    /**
     * 获取文件访问记录
     * @param $data
     */
    public function getFileAccessLog($data){

        $user = auth('api')->user();
        $fileid = $data['id'];
//        $fileid = FunctionTool::decrypt_id($data['id']);
        $file = OssFile::find($fileid);
        //获取访问记录（待定）
        $log = FileUseRecord::where('path',$file->oss_path)->get();
        if($log){
            return json_encode(['status' => 'success', 'message' => '获取记录成功','log'=>$log]);
        }else{
            return json_encode(['status' => 'error', 'message' => '暂无访问记录','log'=>'']);
        }

    }
}