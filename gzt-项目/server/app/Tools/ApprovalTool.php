<?php

namespace App\Tools;

use App\Http\Resources\ApprovalResource;
use App\Http\Resources\ApprovalTamplateResource;
use App\Interfaces\ApprovalInterface;
use App\Models\Approval;
use App\Models\ApprovalTemplate;
use App\Models\ApprovalType;
use App\Models\ApprovalUser;
use App\Models\CollaborativeTask;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\ApprovalTemplateRepository;
use App\Tools\CompanyOssTool;
use App\Tools\DepartmentTool;
use App\Tools\FunctionTool;
use App\Repositories\OssFileRepository;

class ApprovalTool implements ApprovalInterface
{
    private static $approvalTool;
    public $approvalTemplateRepository;
    public $companyNoticeTool;
    public $getDepartmentTool;
    public $ossFileRepository;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->approvalTemplateRepository = ApprovalTemplateRepository::getApprovalTemplateRepository();
        $this->getDepartmentTool = DepartmentTool::getDepartmentTool();
        $this->companyNoticeTool = CompanyNoticeTool::getCompanyNoticeTool();
        $this->ossFileRepository = OssFileRepository::getOssFileRepository();
    }

    /**
     * 单例模式
     */
    public static function getApprovalTool()
    {
        if (self::$approvalTool instanceof self) {
            return self::$approvalTool;
        } else {
            return self::$approvalTool = new self();
        }
    }

    /**
     * 处理评审通审批回调
     * @param $approval:目标审批
     * @param bool $approval_result:审批结果
     */
    protected function pstCallBack($approval,bool $approval_result,$opinion=''): void
    {
        $data = json_decode($approval->extra_data, true);
        $data['callback_result'] = $approval_result;
        $data['opinion'] = $opinion;
        $approval->extra_data=json_encode($data);
        $approval->save();
        PstTool::approvalCallBack($data);
    }

    /**
     * 防止克隆
     */
    private function _clone()
    {
    }

    /**
     * 创建新审批类型
     */
    public function addApprovalType($data)
    {
        // TODO: Implement addApprovalType() method.
        $company_id=auth('api')->user()->current_company_id;
        if(ApprovalType::where('company_id',$company_id)->count()>15){//每个公司审批类型最大上限默认15个
            return [
                'status' => 'reachLimit', // 达到上限标识
                'message'=>'审批类型数量已到达上限'
            ];
        }
        $data = [
            'name' => $data['name'],
            'company_id' => $company_id,
        ];
        //添加审批类型
        $type_id = $this->approvalTemplateRepository->addApprovalType($data)->id;
        return [
            'status' => 'success', 
            'message' => '创建成功', 
            'data' => [
                'type_id'=>$type_id
            ]
        ];
    }

    /**
     * 删除类型
     */
    public function deleteApprovalType($id)
    {
        if (ApprovalType::find($id)->templates->count() == 0) {
            ApprovalType::find($id)->delete();
            return ['status' => 'success', 'message' => '删除成功'];
        } else {
            return ['status' => 'fail', 'message' => '操作失败,该类型下拥有模板'];
        }
    }

    /**
     * 添加自定义模板
     * @param $data
     * @return mixed
     */
    public function addApprovalTemplate($data)
    {
        $user = auth('api')->user();
        if(ApprovalTemplate::where('company_id',$user->current_company_id)->count()>100){//每个公司审批模板最大上限默认100个
            return ['message'=>'审批模板数量已到达上限'];
        }
        $data = [
            'name' => $data['name'],
            'form_template' => array_get($data, 'form_template') == null ? null : json_encode($data['form_template']),
            'process_template' => array_get($data, 'process_template') == null ? null : json_encode($data['process_template']),
            'approval_method' => $data['approval_method'],
            'type_id' => $data['type_id'],
            'cc_user' => array_get($data, 'process_template') == null ? null : json_encode($data['cc_users']),
            'description' => array_get($data, 'description') == null ? null : $data['description'],
            'per' => array_get($data, 'per') == 'all' ? null : json_encode($data['per']),
            'numbering' => array_get($data, 'approval_number') == null ? 'GZT' : $data['approval_number'],
            'company_id' => $user->current_company_id,
        ];
        //添加审批模板
        $this->approvalTemplateRepository->addApprovalTemplate($data);
        return ['status' => 'success', 'message' => '创建成功'];
    }

    /**
     * 查找可用的模板
     */
    public function ablTemplateList($type_id)
    {
        if ($type_id == 'all' || $type_id == null) {//选择按类型查找模板时,默认查找全部
            $type_id = true;
        }
        //获取用户所属的全部部门id
        $tem= $this->approvalTemplateRepository->templateType(auth('api')->user()->current_company_id);
        return $this->mapTem($tem,$type_id);
    }

    /**
     * 搜索框搜索可用模板(通过name)
     */
    public function searchAblTemplate($name)
    {
        $company_id=auth('api')->user()->current_company_id;
        $type=ApprovalType::where('company_id',$company_id)->where('name','like','%'.$name.'%')->get();
        if(count($type)==0){
            $tem=ApprovalTemplate::where('company_id',$company_id)->where('name','like','%'.$name.'%')->get()->groupBy('type_id')->toarray();
            $data=[];
            foreach ($tem as $k=>$v){
                $name=ApprovalType::find($k)->name;
                $data[]=['type_name' => $name,'data'=>$v];
            }
            return $data;
        }else{
            return $this->mapTem($type);
        }
    }
    private function mapTem($obj,$type_id=true)
    {
        $user_all_department=auth('api')->user()->departments->pluck('id')->toarray();
        foreach ($user_all_department as $v){
            $departments_ids=$this->getDepartmentTool->getNodeDescendantsTree(\App\Tools\FunctionTool::encrypt_id($v))->pluck('id')->toarray();
            $user_all_department=array_unique(array_merge($user_all_department,$departments_ids));
        }
        return $obj->map(function ($templateType) use ($type_id, $user_all_department) {//过滤模板,$type_id审批类型id,$user_all_department获取用户所属的全部部门id
        $data = [];
        foreach ($templateType->templates as $v) {
            $staff_ids = [];
            $department_ids = [];
            if ($v->per != null) {
                $staff_ids = json_decode($v->per)->staffId;
                $department_ids = json_decode($v->per)->departmentId;
            }
            if ($v->type_id == $type_id && $v->is_show == 1 && ($v->per == null ||
                    in_array(auth('api')->user()->id, FunctionTool::decrypt_id_array($staff_ids)) ||
                    count(
                        array_intersect($user_all_department, FunctionTool::decrypt_id_array($department_ids))
                    ) > 0
                )) {
                $data[] = ['id' => $v->id, 'name' => $v->name, 'desc' => $v->description];
            }
        }
        return ['type_name' => $templateType->name, 'data' => $data];
    });
    }

    /**
     * 选择一个模板
     * @param $id
     */
    public function selectTem($id)
    {
        $user = auth('api')->user();
        $tem = ApprovalTemplate::where('id', $id)->get()->map(function ($tem) {
            return [
//                'id'=>$tem->id,
                'name' => $tem->name,
                'form_template' => json_decode($tem->form_template),
                'approval_number' => $tem->numbering,
                'process_template' => json_decode($tem->process_template),
                'cc_users' => json_decode($tem->cc_user),
                'type' => ['type_id' => $tem->type_id, 'name' => $tem->approvalType->name],
                'approval_method' => $tem->approval_method,
                'description' => $tem->description,
            ];
        });
        $canTem = ApprovalTemplate::where('is_show', 1)->where('company_id', $user->current_company_id)->get()
            ->map(function ($v) {
                return ['id' => $v->id, 'name' => $v->name];
            });
        return ['tem' => $tem, 'canTem' => $canTem];
    }

    /**
     * 管理模板-模板列表
     */
    public function sysTemList()
    {
//        FunctionTool::del('approval_template');//彻底清除软删除得数据
        $user = auth('api')->user();
        $template = $this->approvalTemplateRepository->templateType($user->current_company_id);
        $enable = ApprovalTamplateResource::collection($template);
        $disable = ApprovalTemplate::where('company_id', $user->current_company_id)->where('is_show', 0)->get()
            ->map(function ($value) {
                return ['id' => $value->id, 'name' => $value->name, 'description' => $value->description, 'approval_method' => $value->approval_method, 'is_show' => $value->is_show, 'updated_time' => Carbon::parse($value->updated_at)->toDateTimeString(), 'per' => ApprovalTemplateRepository::ableRange($value->per)];
            });
        $data = ['enable' => $enable, 'disable' => $disable];
        return json_encode($data);
    }

    /**
     * 审批类型拖拽排序保存入库
     */
    public function saveSequence($array)
    {
        foreach ($array['sort_json'] as $k => $v) {
            $this->approvalTemplateRepository->updeteApprovalType($k, ['sequence' => $v]);
        }
        return ['status' => 'success', 'message' => '保存成功'];
    }

    /**
     * 管理模板--编辑审批类型
     */
    public function editApprovalType($array)
    {
        $this->approvalTemplateRepository->updeteApprovalType($array['id'], ['name' => $array['name']]);
        return ['status' => 'success', 'message' => '编辑成功'];
    }

    /**
     * 编辑模板
     */
    public function editTemplate($id)
    {
        return ApprovalTemplate::where('id', $id)->get()->map(function ($template) {
            return [
                'id' => $template->id,
                'name' => $template->name,
                'form_template' => json_decode($template->form_template),
                'process_template' => json_decode($template->process_template),
                'type' => ['type_id' => $template->type_id, 'type_name' => $template->approvalType->name],
                'approval_method' => $template->approval_method,
                'description' => $template->description,
                'approval_number' => $template->numbering,
                'is_show' => $template->is_show,
                'per' => json_decode($template->per),
                'cc_users' => json_decode($template->cc_user),
            ];
        });
    }

    /**
     * 删除审批模板
     */
    public function deleteTemplate($id)
    {
        ApprovalTemplate::find($id)->delete();
        return ['status' => 'success', 'message' => '删除成功'];
    }

    /**
     * 提交模板编辑
     */
    public function saveEditTemplate($data)
    {
        $user=auth('api')->user();
        $editData = [
            'name' => $data['name'],
            'form_template' => json_encode($data['form_template']),
            'process_template' => json_encode($data['process_template']),
            'per' => array_get($data, 'per') == 'all' ? null : json_encode($data['per']),
            'cc_user' => array_get($data, 'process_template') == null ? null : json_encode($data['cc_users']),
            'description' => $data['description'],
            'type_id' => $data['type_id'],
            'company_id' => $user->current_company_id
        ];
        $this->approvalTemplateRepository->editTemplate($editData, $data['id']);
        return ['status' => 'success', 'message' => '编辑成功'];
    }

    /**
     * 是否启用
     */
    public function isShow($data)
    {
        $editData = [
            'is_show' => $data['is_show'] == 1 ? 0 : 1,
        ];
        $show = $this->approvalTemplateRepository->editTemplate($editData, $data['id']);
        if ($show) {
            return ['status' => 'success', 'message' => $data['is_show'] == 1 ? '关闭成功' : '启用成功', 'data' => $this->sysTemList()];
        } else {
            return ['status' => 'fail', 'message' => '操作失败', 'data' => ['id' => $data['id'], 'is_show' => $data['is_show']]];
        }
    }

    /**
     * 创建审批申请
     */
    public function createApproval(array $request)
    {
        
        $userIds = json_decode(array_get($request,'userIds','[]'), true);

        DB::beginTransaction();
        $approval_id=null;
        try {
            //暂时测试
            $user = $request['type_id']==1?User::find($request['publish_user_id']):auth('api')->user();
            if (true) {
                $form_template=gettype(array_get($request,'form_template',null))==='array'?array_get($request,'form_template',null):json_decode(array_get($request,'form_template',null), true) ;//表单数据
                //将上传文件重组为[关联健=>文件]形式
                $array_files = $this->extractFilesData($form_template);
                //组装审批数据
                $data_approval = $this->makeApprovalData($request, $user, $array_files, $form_template);
                $approval_id = $this->approvalTemplateRepository->approvalInsertGetId($data_approval);//创建审批
                $data_appproval_users = [];
                $approval_user_ids = [];
                $process_template=gettype($request['process_template'])==='array'?$request['process_template']:json_decode($request['process_template'], true);
                foreach ($process_template as $key => $process) {
                    foreach ($process['checkedInfo']['checkedPersonnels'] as $value) {
                        $approval_user_ids[] = FunctionTool::decrypt_id($value['key']);//
                        $data_appproval_user = [
                            'approval_id' => $approval_id,//审批id
                            'approver_id' => FunctionTool::decrypt_id($value['key']),//审批人id
                            'approval_method' => $request['approval_method'],//审批流程方式(自由流程)
                            'approval_level' => ($key+1),//审批处于那个等级
                            'type' => $process['type'],//审批类型(会签)
                            'status' => $key == 0 ? 1 : 0,//我的审批状态
                            'created_at' => date('Y-m-d H:i:s', time()),
                            'updated_at' => date('Y-m-d H:i:s', time()),
                        ];
                        $data_appproval_users[] = $data_appproval_user;
                    }
                }
                DB::table('approval_user')->insert($data_appproval_users);//添加审批人信息
                //处理抄送人方法
                if(array_get($request,'userIds')!==null){
                    $this->insert_approval_cc_my($request, $approval_id, $user);
                }
            }
            /**
             * 上传文件
             */
            $company = Company::find($user->current_company_id);
            $data=[];

            if($array_files!=[]){//$files为关联数组
                $data = CompanyOssTool::uploadFormfile($array_files, [
                    'oss_path' => $company->oss->root_path . 'approval',//文件存入的所在目录
                    'model_id' => $approval_id,//关联模型id
                    'model_type' => Approval::class,//关联模型类名
                    'company_id' => $user->current_company_id,//所属公司id
                    'uploader_id' => $user->id,//上传者id
                ],$form_template);
                //更新新建审批表单数据
                Approval::where('id',$approval_id)->update(['form_template'=>json_encode($form_template)]);
            }

            /**
             * 通知
             */
            $approval = Approval::find($approval_id);
            $notification_way = $request['notification_way'];
            $this->approvalNotify($user,['approval' => $approval, 'content' => $approval->user->name . '发起一个申请需要您审批'
                , 'notification_way' => $notification_way, 'user_ids' => array_unique($approval_user_ids)]);//通知审批人

            $this->approvalNotify($user,['approval' => $approval, 'content' => $approval->user->name . '抄送给您一份审批',
                'notification_way' => $notification_way,
                'user_ids' => array_unique(FunctionTool::decrypt_id_array($userIds))]);//通知被抄送人
            if (count($data) == 0) {
                DB::commit();
                return ['status' => 'success', 'message' => '发布成功'];
            } else {
                DB::rollBack();
                return ['status' => 'fail', 'message' => '文件上传出错!,发布失败!' . $data];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return ['status' => 'error', 'message' => '服务器错误!!'];
        }
    }

    /**
     * 组装审批主表数据
     * @param array $data
     * @param $user
     * @param $array_files
     * @param $form_template
     * @return array
     */
    protected function makeApprovalData(array $data, $user, $array_files, $form_template): array
    {
        return [
            'applicant' => $user->id,    //该申请发起人
//            'name' => array_get($data,'name'),                          //该申请名称
            'type_id' => $data['type_id'],                    //该申请属于的大类
            'form_template' => count($array_files) == 0 ? json_encode($form_template) : null,        //表单数据json储存
            'process_template' => $data['process_template'],  //流程数据json储存
//            'description' => '此处是假数据,正确数据需要从表单中摘取',            //对该申请的描述信息
            'company_id' => $user->current_company_id,//该申请处于那个公司
            'end_status' => 0,                                 //通过状态,默认0 未通过
            'approval_method' => $data['approval_method'],//流程类型
            'numbering' => $data['approval_number'] . '-' . $user->id . time(),//审批编号
            'cc_my' =>array_get($data,'cc_users', json_encode([])),//存放抄送人原信息
            'created_at' => date('Y-m-d H:i:s', time()),
            'updated_at' => date('Y-m-d H:i:s', time()),
            'extra_data'=>json_encode(array_get($data,'extra_data',[])),
            'related_pst_id'=>array_get($data,'related_pst_id',0),
        ];
    }
    /**
     * 审批--处理抄送人数据
     * @param array $data:上级传递过来的总数据
     * @param $approval_id:审批的id
     * @param $user:当前操作的用户
     * @param $data_ccs:抄送人信息数组
     */
    protected function insert_approval_cc_my(array $data, $approval_id, $user ): void
    {
        $data_ccs=[];
            foreach (FunctionTool::decrypt_id_array(json_decode($data['userIds'], true)) as $id) {
                $data_cc = [
                    'user_id' => $id,
                    'approval_id' => $approval_id,
                    'type_id' => $data['type_id'],
                    'company_id' => $user->current_company_id,
                    'created_at' => date('Y-m-d H:i:s', time()),
                    'updated_at' => date('Y-m-d H:i:s', time()),
                ];
                $data_ccs[] = $data_cc;
            }
            DB::table('approval_cc_my')->insert($data_ccs);//添加抄送人信息
    }
    /**
     * 创建审批时抽取上传的文件信息
     * @param $form_template:表单数据
     * @param $array_files:存储文件的数组
     * @return mixed
     */
    protected function extractFilesData($form_template)
    {
        $array_files=[];
        if ($form_template != null && $form_template != []) {
            foreach ($form_template as $k => $v) {
                if ($v['type'] == 'ANNEX' && isset($v['value'])) {//$v['field']['value']为数组,至少包含一个文件
                    $array_files[$k] = $v['value'];
                }
            }
        }
        return $array_files;
    }

    /**
     * 再次申请按钮
     */
    public function againApply(Request $request)
    {
        $user = auth('api')->user();
        $tem = Approval::where('id', FunctionTool::decrypt_id($request->id))->get()->map(function ($tem) {
            return [
//                'id'=>$tem->id,
                'name' => $tem->name,
                'form_template' => json_decode($tem->form_template),
                'approval_number' => $tem->numbering,
                'process_template' => json_decode($tem->process_template),
                'cc_users' => json_decode($tem->cc_my),
                'type' => ['type_id' => $tem->type_id, 'name' => $tem->approvalType->name],
                'approval_method' => $tem->approval_method,
                'description' => $tem->description,
            ];
        });
        $canTem = ApprovalTemplate::where('is_show', 1)->where('company_id', $user->current_company_id)->get()
            ->map(function ($v) {
                return ['id' => $v->id, 'name' => $v->name];
            });
        return ['tem' => $tem, 'canTem' => $canTem];
//        $id = $request->id;
//        $data = Approval::where('id', $id)->get()->map(function ($approval) {
//            return [
////                'id'=>$tem->id,
//                'name' => $approval->name,
//                'form_template' => json_decode($approval->form_template),
//                'approval_number' => $approval->numbering,
//                'process_template' => json_decode($approval->process_template),
//                'cc_users' => json_decode($approval->cc_my),
//                'type' => ['type_id' => $approval->type_id, 'name' => $approval->approvalType->name],
//                'approval_method' => $approval->approval_method,
//                'description' => $approval->description,
//            ];
//        });
//        return $data;
    }

    /**
     * 审批列表
     */
    public function ApprovalList($data)
    {
        $page = array_get($data, 'page', 1);
        $page_size = array_get($data, 'page_size', 10);
        $number=array_get($data, 'number', '%');
        if ($data['type'] == 'pending') {//待我审批的
            $arr = $this->pendingApproval($data['type_id'], $page, $page_size,$number);
        } elseif ($data['type'] == 'approved') {//我已审批的
            $arr = $this->approvalCompleted($data['type_id'], $data['status'], $page, $page_size,$number);
        } elseif ($data['type'] == 'initiate') {//我发起的
            $arr = $this->initiatedApproval($data['type_id'], $data['status'], $page, $page_size,$number);
        } elseif ($data['type'] == 'ccApproval') {//抄送给我的
            $arr = $this->ccMy($data['type_id'], $data['status'], $page, $page_size,$number);
        } elseif ($data['type'] == 'archive') {//已归档的
            $arr = $this->archived($data['type_id'], $page, $page_size,$number);
        }
        $approval = $arr[0];
        $all_count = $arr[1];
        $approval_data = [];
        foreach ($approval as $k => $v) {
            $obj_approval = ApprovalUser::where('approval_id', $v->id)->where('level_status', 0)->where('status', 1)->first();
            $approver_name = $obj_approval == null ? null : $obj_approval->user['name'];
            $status = $v->cancel_or_archive == 1 ? '已撤销' : ($v->cancel_or_archive == 2 ? '已归档' : ($v->end_status == 0 ? '审批中' : ($v->end_status == 1 ? '已通过' : '已拒绝')));
            $approval_data[] = [
                'id' =>FunctionTool::encrypt_id($v->id),
                'type' => $v->type_name,
                'sponsor' => $v->sponsor_name,
                'created_at' => $v->created_at,//发起时间
                'complete_time' => $v->complete_time == null ? '进行中' : $v->complete_time,//完成时间
                'archive_time'=>$v->archive_time,//归档时间
                'approver' => $approver_name,//审批人
                'content' => $this->approvalContent(json_decode($v->form_template)),
                'currentState' => $status,
            ];
        }
        $data = ['all_count' => $all_count, 'approval_data' => $approval_data];
        return json_encode($data);
    }

    /**
     * 审批详情
     */
    public function detail($id)
    {
        $data = new ApprovalResource(Approval::find(FunctionTool::decrypt_id($id)));
        return json_encode($data);
    }


    /**
     * 审批操作(同意)
     */
    public function agree($approval_id, $opinion, $notification_way)
    {
        $approval_id=FunctionTool::decrypt_id($approval_id);
        try {
            DB::beginTransaction();
            $user = auth('api')->user();
            $ApprovalUser = $this->approvalTemplateRepository->agreeFind($approval_id, $user->id);
            $type = $ApprovalUser->type;
            $approval_level = $ApprovalUser->approval_level;
            $id = $ApprovalUser->id;
            if ($type == 'countersign') {
                $this->approvalTemplateRepository->agreeUpdate($id, ['status' => 2, 'opinion' => $opinion,'complete_time'=>date('Y-m-d H:i:s',time())]);
                $status_array = $this->approvalTemplateRepository->agreeFindStatus($approval_id, $approval_level);
                if (count(array_unique($status_array)) == 1) {//则该级通过,改变下一级审批状态,并通知相关评审人
                    $this->approvalTemplateRepository->fish_level($approval_id, $approval_level);
                    $next_level = $this->approvalTemplateRepository->updateNextLevel($approval_id, $approval_level);
                    if ($next_level == 0) {//没有下一级审批,则改变该审批最终状态为通过
                        $this->approvalTemplateRepository->updateEndStatus($approval_id, ['end_status' => 1, 'complete_time' => date('Y-m-d H:i:s', time())]);
                        /**
                         * 通知
                         */
                        //组装动态列表单个数据格式
                        $approval = Approval::find($approval_id);
                        $cc_users =$approval->cc->pluck('user_id')->toarray();//抄送人id
                        $approval_users =$approval->approvalUsers->filter(function ($obj){//已审批人id
                            return $obj->status!=0&&$obj->status==4;//!in_array([0,4],$obj->status)
                        })->pluck('approver_id')->toarray();
                        $user_ids=array_unique(array_merge($cc_users,$approval_users));
                        $user_ids[]=$approval->applicant;//发起人id
                        $this->approvalNotify($user,['approval' => $approval, 'content' => '审批已结束', 'notification_way' => $notification_way, 'user_ids' => $user_ids]);//通知所有相关人,该审批结束
                        //处理pst通发起审批回调
                        if (!empty(json_decode($approval->extra_data))) {
                            $this->pstCallBack($approval, true, $opinion);
                        }
                    }else{
                        /**
                         * 通知
                         */
                        //组装动态列表单个数据格式
                        $approval = Approval::find($approval_id);
                        $approval_users =$approval->approvalUsers->filter(function ($obj){
                            return $obj->status==1&&$obj->level_status==0;//!in_array([0,4],$obj->status)
                        })->pluck('approver_id')->toarray();
                        $user_ids=array_unique($approval_users);
                        $this->approvalNotify($user,['approval' => $approval, 'content' => '您有你个待审批的申请', 'notification_way' => $notification_way, 'user_ids' => $user_ids]);//通知下一级评审人去评审
                    }
                }
                DB::commit();
                return ['status' => 'success', 'message' => '操作成功'];
            } else {//普通和或签
                $this->approvalTemplateRepository->agreeUpdate($id, ['status' => 2, 'opinion' => $opinion,'complete_time'=>date('Y-m-d H:i:s',time())]);
                $this->approvalTemplateRepository->fish_level($approval_id, $approval_level);
                $next_level = $this->approvalTemplateRepository->updateNextLevel($approval_id, $approval_level);
                if ($next_level == 0) {//没有下一级审批,则改变该审批最终状态为通过
                    $this->approvalTemplateRepository->updateEndStatus($approval_id, ['end_status' => 1, 'complete_time' => date('Y-m-d H:i:s', time())]);
                    /**
                     * 通知
                     */
                    //组装动态列表单个数据格式
                        $approval = Approval::find($approval_id);
                        $cc_users =$approval->cc->pluck('user_id')->toarray();//抄送人id
                        $approval_users =$approval->approvalUsers->filter(function ($obj){//已审批人id
                            return $obj->status!=0&&$obj->status==4;//!in_array([0,4],$obj->status)
                        })->pluck('approver_id')->toarray();
                        $user_ids=array_unique(array_merge($cc_users,$approval_users));
                        $user_ids[]=$approval->applicant;//发起人id
                        $this->approvalNotify($user,['approval' => $approval, 'content' => '审批已结束', 'notification_way' => $notification_way, 'user_ids' => $user_ids]);//通知所有相关人,该审批结束
                    //处理pst通发起审批回调
                    if (!empty(json_decode($approval->extra_data))) {
                        $this->pstCallBack($approval, true, $opinion);
                    }
                }else{
                    /**
                     * 通知
                     */
                    //组装动态列表单个数据格式
                        $approval = Approval::find($approval_id);
                        $approval_users =$approval->approvalUsers->filter(function ($obj){
                            return $obj->status==1&&$obj->level_status==0;//!in_array([0,4],$obj->status)
                        })->pluck('approver_id')->toarray();
                        $user_ids=array_unique($approval_users);
                        $this->approvalNotify($user,['approval' => $approval, 'content' => '您有你个待审批的申请', 'notification_way' => $notification_way, 'user_ids' => $user_ids]);//通知下一级评审人去评审
                }
                DB::commit();
                return ['status' => 'success', 'message' => '操作成功'];
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return ['status'=>'error','message' => '服务器错误!!'];
        }
    }

    /**
     * 审批操作(拒绝)
     */
    public function refuse($approval_id, $opinion, $notification_way)
    {
        $approval_id=FunctionTool::decrypt_id($approval_id);
        DB::beginTransaction();
        try {
            $user = auth('api')->user();
            $ApprovalUser = $this->approvalTemplateRepository->agreeFind($approval_id, $user->id);
            $type = $ApprovalUser->type;
            $approval_level = $ApprovalUser->approval_level;
            $id = $ApprovalUser->id;
            $this->approvalTemplateRepository->agreeUpdate($id, ['status' => 3, 'opinion' => $opinion,'complete_time'=>date('Y-m-d H:i:s',time())]);
            $this->approvalTemplateRepository->fish_level($approval_id, $approval_level);
            $this->approvalTemplateRepository->updateEndStatus($approval_id, ['end_status' => 2, 'complete_time' => date('Y-m-d H:i:s', time())]);
            /**
             * 通知
             */
            //组装动态列表单个数据格式
            $approval = Approval::find($approval_id);
            $user_ids=[$approval->applicant];
            $content = '您的审批申请已被拒绝,拒绝意见-' . $opinion;//通知内容
            $this->approvalNotify($user,['approval' => $approval, 'content' => $content, 'notification_way' => $notification_way, 'user_ids' => $user_ids]);
            //处理pst通发起审批回调
            if (!empty(json_decode($approval->extra_data))) {
                $this->pstCallBack($approval, false, $opinion);
            }
            DB::commit();
            return ['status' => 'success', 'message' => '操作成功'];
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return ['status'=>'error','message' => '服务器错误'];
        }
    }

    /**
     * 转交
     */
    public function transfer($data)
    {
        $approval_id=FunctionTool::decrypt_id($data['approval_id']);
        try {
            DB::beginTransaction();
            $user = auth('api')->user();
            if ($user->id == FunctionTool::decrypt_id($data['transferee_id'])) {
                return ['status' => 'fail','message' => '不能转交给自己'];
            }
            $transferee_id = FunctionTool::decrypt_id($data['transferee_id']);//被转交人的id
            $opinion = $data['opinion'];//转交意见
            $ApprovalUser = ApprovalUser::where('approval_id', $approval_id)->where('approver_id', $user->id)->where('status', 1)->where('level_status', 0)->first();
            $new_data = $ApprovalUser->toarray();
            if (count($ApprovalUser->toarray()) != 0) {//不为空
                $ApprovalUser->update(['status' => 4, 'opinion' => $opinion,'transferee_id'=>$transferee_id,'complete_time'=>date('Y-m-d H:i:s',time())]);
                $new_data['approver_id'] = $transferee_id;
                $new_data['created_at'] = date('Y-m-d H:i:s', time());
                $new_data['updated_at'] = date('Y-m-d H:i:s', time());
                unset($new_data['id']);
                unset($new_data['opinion']);
                unset($new_data['complete_time']);
                ApprovalUser::create($new_data);
                /**
                 * 通知
                 */
                //组装动态列表单个数据格式
                $approval = Approval::find(FunctionTool::decrypt_id($data['approval_id']));
                $content = '转交人:' . $user->name . ',转交意见-' . $opinion;//通知内容
                $this->approvalNotify($user,['approval' => $approval, 'content' => $content, 'notification_way' => $data['notification_way'], 'user_ids' => [$transferee_id]]);
                DB::commit();
                return ['status' => 'success', 'message' => '转交成功'];
            } else {
                DB::rollBack();
                return ['status' => 'fail', 'message' => '转交失败'];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => '转交失败，'];
        }
    }

    /**
     * 审批操作(撤销)
     */
    public function cancel($approval_id, $opinion)
    {
        $approval_id=FunctionTool::decrypt_id($approval_id);
        $user = auth('api')->user();
        $cancel = $this->approvalTemplateRepository->cancel($approval_id, $user->id, $opinion);
        if ($cancel) {
            /**
             * 通知
             */
            //组装动态列表单个数据格式
            $approval = Approval::find($approval_id);
            $cc_users =$approval->cc->pluck('user_id')->toarray();
            $approval_users =$approval->approvalUsers->filter(function ($obj){
                return $obj->status!=0&&$obj->status==4;//!in_array([0,4],$obj->status)
            })->pluck('approver_id')->toarray();
            $user_ids=array_unique(array_merge($cc_users,$approval_users));
            $content = '编号为'.$approval->numbering.'审批已撤销,撤销意见-' . $opinion;//通知内容
            $this->approvalNotify($user,['approval' => $approval, 'content' => $content, 'notification_way' => ['need_email'=>0, 'need_notify'=> 1, 'need_sms'=> 0, 'need_voice_sms'=>0], 'user_ids' => $user_ids]);
            return ['status' => 'success', 'message' => '撤销成功'];
        } else {
            return ['status' => 'fail', 'message' => '撤销失败'];
        }
    }

    /**
     * 归档
     * @param $id
     * @param $user_id
     * @return mixed
     */
    public function archive($approval_id, $opinion)
    {
        $opinion=$opinion===null?'':$opinion;
        $approval_id=FunctionTool::decrypt_id($approval_id);
        $user = auth('api')->user();
        $archive = $this->approvalTemplateRepository->archive($approval_id, $user->id, $opinion);
        if ($archive) {
            return ['status' => 'success', 'message' => '归档成功'];
        } else {
            return ['status' => 'fail', 'message' => '归档失败'];
        }

    }

    /**
     * 催办
     */
    public function urgent(Request $request)
    {
        $user=auth('api')->user();
        $notification_way=isset($request->notification_way)?$request->notification_way:['need_email'=>0, 'need_notify'=> 1, 'need_sms'=> 0, 'need_voice_sms'=>0];//催办默认通知方式为实时通知
        $id = FunctionTool::decrypt_id($request->approval_id);//审批id
        $contents = $request->opinion;//催办内容
        $user_ids = DB::table('approval_user')
            ->where('approval_id', $id)
            ->where('status', 1)
            ->where('level_status', 0)
            ->pluck('approver_id');
        $user_ids = array_unique($user_ids->toarray());//正在审批人ids
        /**
         * 通知
         */
        //组装动态列表单个数据格式
        $approval = Approval::find($id);
        $content = $approval->name . '催办:' . $contents;//通知内容
        $this->approvalNotify($user,['approval' => $approval, 'content' => $content, 'notification_way' => $notification_way, 'user_ids' => $user_ids]);
//        dd($user_ids);
        return ['status' => 'success', 'message' => '已催办'];
    }

    /**
     * 审批经典模板
     */
    public function approvalClassicTemplate()
    {
        return ApprovalType::where('company_id', 0)->get()
            ->map(function ($templateType) {
                $data = [];
                foreach ($templateType->templates as $v) {
                    $data[] = ['id' => $v->id, 'name' => $v->name, 'desc' => $v->description];
                }
                return ['type_name' => $templateType->name, 'data' => $data];
            });
    }

    /**
     * 已有模板
     */
    public function existingTemplate()
    {
        return $this->approvalTemplateRepository->templateType(auth('api')->user()->current_company_id)
            ->map(function ($templateType) {
                $data = [];
                foreach ($templateType->templates as $v) {
                    $data[] = ['id' => $v->id, 'name' => $v->name, 'desc' => $v->description];
                }
                return ['type_name' => $templateType->name, 'data' => $data];
            });
    }

    /**
     * 审批类型列表
     */
    public function approvalTypeList()
    {
        $user = auth('api')->user();
        return ApprovalType::where('company_id', $user->current_company_id)->orderBy('sequence', 'asc')->orderBy('updated_at', 'desc')->get()
            ->map(function ($value) {
                return ['type_id' => $value->id, 'name' => $value->name, 'all_count' => $value->approvals->count()];
            });
    }

    /**
     * 删除附件(只删除附件关系表中的关系)
     */
    public function deleteFile(Request $request)
    {
//        $file_id = $request->file_id;
//        $model_id = DB::table('model_has_file')->where('file_id', $file_id)->where('model_type', Approval::class)->value('model_id');
//        $data = [
//            'file_id' => $file_id,
//            'model_id' => $model_id,
//            'model_type' => Approval::class,
//        ];
//        $this->ossFileRepository->removeFileRelation($data);
        return ['status' => 'success', 'message' => '删除成功'];
    }

    /**
     * 待我审批的
     */
    private function pendingApproval($type_id, $page, $page_size,$number)
    {
        $user = auth('api')->user();
        if ($type_id == 'all') {
            $type_id = '%';
        }
        if($user->current_company_id===0){
            $private='!=';
        }else{
            $private='=';
        }
        $count = DB::table('approval')
            ->where('numbering', 'LIKE', '%'.$number.'%')
            ->where('type_id', 'LIKE', $type_id)
            ->where('cancel_or_archive', 0)
            ->join('approval_user', function ($join) use ($user,$private) {
                $join->on('approval.id', '=', 'approval_user.approval_id')
                    ->where('approval_user.status', '=', 1)
                    ->where('approval_user.approver_id', '=', $user->id)
                    ->where('approval_user.level_status', '=', 0)
                    ->where('approval.company_id', $private, $user->current_company_id);
            })
            ->count();
        $approval = DB::table('approval')
            ->where('numbering', 'LIKE', '%'.$number.'%')
            ->where('type_id', 'LIKE', $type_id)
            ->where('cancel_or_archive', 0)
            ->join('approval_user', function ($join) use ($user,$private) {
                $join->on('approval.id', '=', 'approval_user.approval_id')
                    ->where('approval_user.status', '=', 1)
                    ->where('approval_user.approver_id', '=', $user->id)
                    ->where('approval_user.level_status', '=', 0)
                    ->where('approval.company_id', $private, $user->current_company_id);
            })
            ->join('approval_type', function ($join) use ($user) {
                $join->on('approval.type_id', '=', 'approval_type.id');
            })
            ->join('users', function ($join) use ($user) {
                $join->on('approval.applicant', '=', 'users.id');
            })
            ->select('approval.*', 'approval_type.name as type_name', 'users.name', 'users.name as sponsor_name')
            ->offset(($page - 1)*$page_size)
            ->limit($page_size - 0)
            ->orderBy('updated_at', 'desc')
            ->get();
        return [$approval, $count];
    }

    /**
     * 我已审批的
     */
    private function approvalCompleted($type_id, $status, $page, $page_size,$number)
    {
        $user = auth('api')->user();
        if ($type_id == 'all') {
            $type_id = '%';
        }
        if($user->current_company_id===0){
            $private='!=';
        }else{
            $private='=';
        }
        if ($status == 'all') {//所有状态的
            $col_status = 'end_status';
            $status = '%';
        } elseif ($status == 'approval') {//审批中
            $col_status = 'end_status';
            $status = 0;
        } elseif ($status == 'approvalPassed') {//审批通过
            $col_status = 'end_status';
            $status = 1;
        } elseif ($status == 'approvalNotPassed') {//审批不通过
            $col_status = 'end_status';
            $status = 2;
        } elseif ($status == 'revoked') {//审批撤销
            $col_status = 'cancel_or_archive';
            $status = 1;
        } elseif ($status == '归档') {//审批归档
            $col_status = 'cancel_or_archive';
            $status = 2;
        }

        $count = DB::table('approval')
            ->where('numbering', 'LIKE', '%'.$number.'%')
            ->where('type_id', 'LIKE', $type_id)
            ->where($col_status, 'LIKE', $status)
            ->join('approval_user', function ($join) use ($user,$private) {
                $join->on('approval.id', '=', 'approval_user.approval_id')
                    ->whereIn('approval_user.status', [2, 3])
                    ->where('approval_user.approver_id', '=', $user->id)
                    ->where('approval.company_id', $private, $user->current_company_id);
            })
            ->join('approval_type', function ($join) use ($user) {
                $join->on('approval.type_id', '=', 'approval_type.id');
            })
            ->join('users', function ($join) use ($user) {
                $join->on('approval.applicant', '=', 'users.id');
            })
            ->select('approval.*', 'approval_type.name as type_name', 'users.name as sponsor_name')
            ->distinct()
            ->get();
        $approval = DB::table('approval')
            ->where('numbering', 'LIKE', '%'.$number.'%')
            ->where('type_id', 'LIKE', $type_id)
            ->where($col_status, 'LIKE', $status)
            ->join('approval_user', function ($join) use ($user,$private) {
                $join->on('approval.id', '=', 'approval_user.approval_id')
                    ->whereIn('approval_user.status', [2, 3])
                    ->where('approval_user.approver_id', '=', $user->id)
                    ->where('approval.company_id',$private, $user->current_company_id);
            })
            ->join('approval_type', function ($join) use ($user) {
                $join->on('approval.type_id', '=', 'approval_type.id');
            })
            ->join('users', function ($join) use ($user) {
                $join->on('approval.applicant', '=', 'users.id');
            })
            ->select('approval.*', 'approval_type.name as type_name', 'users.name as sponsor_name')
            ->offset(($page - 1)*$page_size)
            ->limit($page_size - 0)
            ->orderBy('updated_at', 'desc')
            ->distinct()
            ->get();
        return [$approval, count($count)];
    }

    /**
     * 我发起的审批
     */
    private function initiatedApproval($type_id, $status, $page, $page_size,$number)
    {
        $user = auth('api')->user();
        if ($type_id == 'all') {
            $type_id = '%';
        }
        if ($status == 'all') {//所有状态的
            $col_status = 'end_status';
            $status = '%';
        } elseif ($status == 'approval') {//审批中
            $col_status = 'end_status';
            $status = 0;
        } elseif ($status == 'approvalPassed') {//审批通过
            $col_status = 'end_status';
            $status = 1;
        } elseif ($status == 'approvalNotPassed') {//审批不通过
            $col_status = 'end_status';
            $status = 2;
        } elseif ($status == 'revoked') {//审批撤销
            $col_status = 'cancel_or_archive';
            $status = 1;
        } elseif ($status == '归档') {//审批归档
            $col_status = 'cancel_or_archive';
            $status = 2;
        }
        $count = DB::table('approval')
            ->where('numbering', 'LIKE', '%'.$number.'%')
            ->where('type_id', 'LIKE', $type_id)
            ->where($col_status, 'LIKE', $status)
            ->where('applicant', 'LIKE', $user->id)
            ->count();
        $approval = DB::table('approval')
            ->where('numbering', 'LIKE', '%'.$number.'%')
            ->where('type_id', 'LIKE', $type_id)
            ->where($col_status, 'LIKE', $status)
            ->where('applicant', 'LIKE', $user->id)
            ->join('approval_type', function ($join) use ($user) {
                $join->on('approval.type_id', '=', 'approval_type.id');
            })
            ->join('users', function ($join) use ($user) {
                $join->on('approval.applicant', '=', 'users.id');
            })
            ->select('approval.*', 'approval_type.name as type_name', 'users.name as sponsor_name')
            ->offset(($page - 1)*$page_size)
            ->limit($page_size - 0)
            ->orderBy('updated_at', 'desc')
            ->get();
        return [$approval, $count];
    }

    /**
     * 抄送我的
     */
    private function ccMy($type_id, $status, $page, $page_size,$number)
    {
        $user = auth('api')->user();
        if ($type_id == 'all') {
            $type_id = '%';
        }
        if($user->current_company_id===0){
            $private='!=';
        }else{
            $private='=';
        }
        if ($status == 'all') {//所有状态的
            $col_status = 'end_status';
            $status = '%';
        } elseif ($status == 'approval') {//审批中
            $col_status = 'end_status';
            $status = 0;
        } elseif ($status == 'approvalPassed') {//审批通过
            $col_status = 'end_status';
            $status = 1;
        } elseif ($status == 'approvalNotPassed') {//审批不通过
            $col_status = 'end_status';
            $status = 2;
        } elseif ($status == 'revoked') {//审批撤销
            $col_status = 'cancel_or_archive';
            $status = 1;
        } elseif ($status == '归档') {//审批归档
            $col_status = 'cancel_or_archive';
            $status = 2;
        }
        $count = DB::table('approval')
            ->where('numbering', 'LIKE', '%'.$number.'%')
            ->where($col_status, 'LIKE', $status)
            ->where('approval.type_id', 'LIKE', $type_id)
            ->join('approval_cc_my', function ($join) use ($user, $type_id,$private) {
                $join->on('approval.id', '=', 'approval_cc_my.approval_id')
                    ->where('approval_cc_my.user_id', '=', $user->id)
                    ->where('approval.company_id', $private, $user->current_company_id);
            })
            ->count();
        $approval = DB::table('approval')
            ->where('numbering', 'LIKE', '%'.$number.'%')
            ->where($col_status, 'LIKE', $status)
            ->where('approval.type_id', 'LIKE', $type_id)
            ->join('approval_cc_my', function ($join) use ($user, $type_id,$private) {
                $join->on('approval.id', '=', 'approval_cc_my.approval_id')
                    ->where('approval_cc_my.user_id', '=', $user->id)
                    ->where('approval.company_id',$private, $user->current_company_id);
            })
            ->join('approval_type', function ($join) use ($user) {
                $join->on('approval.type_id', '=', 'approval_type.id');
            })
            ->join('users', function ($join) use ($user) {
                $join->on('approval.applicant', '=', 'users.id');
            })
            ->select('approval.*', 'approval_type.name as type_name', 'users.name as sponsor_name')
            ->offset(($page - 1)*$page_size)
            ->limit($page_size - 0)
            ->orderBy('updated_at', 'desc')
            ->get();
        return [$approval, $count];
    }

    /**
     * 已归档的(归档由发起人归档)
     */
    private function archived($type_id, $page, $page_size,$number)
    {
        $user = auth('api')->user();
        if ($type_id == 'all') {
            $type_id = '%';
        }
        $count = DB::table('approval')
            ->where('numbering', 'LIKE', '%'.$number.'%')
            ->where('approval.type_id', 'LIKE', $type_id)
            ->where('cancel_or_archive', 2)
            ->count();
        $approval = DB::table('approval')
            ->where('numbering', 'LIKE', '%'.$number.'%')
            ->where('approval.type_id', 'LIKE', $type_id)
            ->where('cancel_or_archive', 2)
            ->join('approval_type', function ($join) use ($user) {
                $join->on('approval.type_id', '=', 'approval_type.id');
            })
            ->join('users', function ($join) use ($user) {
                $join->on('approval.applicant', '=', 'users.id');
            })
            ->select('approval.*', 'approval_type.name as type_name', 'users.name as sponsor_name')
            ->offset(($page - 1)*$page_size)
            ->limit($page_size - 0)
            ->orderBy('updated_at', 'desc')
            ->get();
        return [$approval, $count];
    }

    /**
     * @param $data
     * @throws \ReflectionException
     * 审批通知
     */
    private function approvalNotify(User $user,array $data)
    {
        $company = Company::find($user->current_company_id);
        $single_data = DynamicTool::getSingleListData(Approval::class, 1, 'company_id', $company->id,
            $company->name, array_get($data, 'content'), date('Y-m_d H:i:s', time()));
        return NotifyTool::publishNotify(array_get($data, 'user_ids'), $user->current_company_id, array_get($data, 'approval'), array_get($data, 'notification_way')===null?[]:(gettype($data['notification_way'])=='array'?$data['notification_way']:json_decode($data['notification_way'],true)), $single_data,[]);//此处方法顺序待调整
    }

    /**
     * 审批内容(摘要)
     */
    static function approvalContent($data)
    {
        $approval_content=[];
        $i=0;
        if($data!=null){
            foreach ($data as $v){
                if(!isset($v->value)){
                    continue;
                }
                $i++;
                if ($v->type=='INPUT'){//单行文本
                    $approval_content[]=$v;
                }elseif ($v->type=='MONEY'){//金额
                    $approval_content[]=$v;
                }elseif ($v->type=='DATEPICKER'){//日期
                    $approval_content[]=$v;
                }elseif ($v->type=='DATERANGE'){//日期区间
                    $approval_content[]=$v;
                }
                if($i==3){//只摘取3组数据
                    break;
                }
            }
            $count=4-count($approval_content);
            if($count>0){
                $i=0;
                foreach ($data as $v){
                    if(!isset($v->value)){
                        continue;
                    }
                    $i++;
                    if($v->type!='INPUT'&&$v->type!='MONEY'&&$v->type!='DATEPICKER'&&$v->type!='DATERANGE'&&$v->type!='ANNEX'){
                        $approval_content[]=$v;
                    }
                    if($i==$count){//只摘取3组数据
                        break;
                    }
                }
            }
        }
        return $approval_content;
    }
    /**
     * 审批模板默认数据
     */
    public static function giveTemplateBasisData($company_id)
    {
        //赋予公司初始评审通模板及模板分组
        ApprovalType::where('company_id',0)->get()->map(function ($approval_type) use ($company_id){
            $type=$approval_type->toarray();
            $type['company_id']=$company_id;
            unset($type['id']);
            //插入模板类型
            $type_id=DB::table('approval_type')->insertGetId($type);
            $approval_templates=[];
            $approvalTemplates=$approval_type->templates->toarray();
            foreach ($approvalTemplates as $approvalTemplate){
                unset($approvalTemplate['id']);
                $approvalTemplate['type_id']=$type_id;
                $approvalTemplate['company_id']=$company_id;
                $approval_templates[]=$approvalTemplate;
            }
            //插入模板数据
            DB::table('approval_template')->insert($approval_templates);
        });
    }
    /**
     * 被关联的协助列表
     */
    private function assistData($pst_id)
    {
        $task=CollaborativeTask::where('pst_id',$pst_id)->get();
        //协助任务参与人信息
        $invitations=$task->invitations;
        return ['status'=>'success','data'=>['task'=>$task,'$invitations'=>$invitations]];
    }
}
