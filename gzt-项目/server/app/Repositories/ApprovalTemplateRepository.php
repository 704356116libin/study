<?php

namespace App\Repositories;

use App\Models\ApprovalTemplate;
use App\Models\ApprovalType;
use App\Models\ApprovalCcMy;
use App\Models\ApprovalUser;
use App\Models\Approval;
use App\Models\Department;
use App\Models\User;
use App\Tools\DepartmentTool;
use App\Tools\FunctionTool;
use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/9
 * Time: 11:45
 */
class ApprovalTemplateRepository
{
    private static $approvalTemplateRepository;
    public $getDepartmentTool;
    /**
     * 构造函数(该类使用单例模式)
     * CollaborativeRepository constructor.
     */
    public function __construct()
    {
        $this->getDepartmentTool=DepartmentTool::getDepartmentTool();
    }

    /**
     * 实例化自身类(单例模式)
     */
    public static function getApprovalTemplateRepository()
    {
        if (self::$approvalTemplateRepository instanceof self) {
            return self::$approvalTemplateRepository;
        } else {
            return self::$approvalTemplateRepository = new self;
        }
    }

    /**
     * 防止被克隆
     */
    private function _clone()
    {
    }

    /**
     * 添加审批类型
     */
    public function addApprovalType($data)
    {
        return ApprovalType::create($data);
    }

    /**
     * 更新审批类型
     */
    public function updeteApprovalType($id,$data)
    {
        return ApprovalType::where('id',$id)->update($data);
    }

    /**
     * 添加流程模板
     */
    public function addApprovalTemplate($data)
    {
        return ApprovalTemplate::create($data);
    }
    /**
     * 保存编辑模板
     */
    public function editTemplate($data,$id)
    {
        return ApprovalTemplate::where('id',$id)->where('company_id','!=',0)->update($data);
    }

    /**
     * 查找所有模板类型
     */
    public function templateType($company_id)
    {
        return ApprovalType::where('company_id', $company_id)->orderBy('sequence', 'asc')->orderBy('updated_at', 'desc')->get();
    }


    /**
     * 创建审批,返回主键id
     */
    public function approvalInsertGetId($data_approval)
    {
        return Approval::insertGetId($data_approval);
    }

    /**
     * 添加审批人信息
     */
    public function approvalUserCreate($data_appproval_user)
    {
        return ApprovalUser::create($data_appproval_user);
    }

    /**
     * 添加抄送人
     */
    public function ccCreate($data_cc)
    {
        return ApprovalCcMy::create($data_cc);
    }

    /**
     * 查找我审批的信息
     */
    public function agreeFind($approval_id, $user_id)
    {
        return ApprovalUser::where('approval_id', $approval_id)->where('approver_id', $user_id)->where('status', 1)->first();
    }

    /**
     * 我同意该审批
     * @param $id
     * @return mixed
     */
    public function agreeUpdate($id, $data)
    {
        return ApprovalUser::where('id', $id)->update($data);
    }

    /**
     * 撤销
     */
    public function cancel($id, $user_id,$opinion)
    {
        return Approval::where('id', $id)->where('applicant', $user_id)->where('end_status', 0)->update(['cancel_or_archive' => 1,'end_status'=>3,'opinion'=>$opinion]);
    }

    /**
     * 归档
     */
    public function archive($id, $user_id,$opinion)
    {
        return Approval::where('id', $id)->where('applicant', $user_id)->update(['cancel_or_archive' => 2,'opinion'=>$opinion,'end_status'=>3,'archive_time'=>date('Y-m-d H:i:s',time())]);
    }

    /**
     * 查找该级状态
     */
    public function agreeFindStatus($approval_id, $approval_level)
    {
        return ApprovalUser::where('approval_id', $approval_id)->where('approval_level', $approval_level)->pluck('status')->toArray();
    }

    /**
     * 结束该级
     */
    public function fish_level($approval_id, $approval_level)
    {
        return ApprovalUser::where('approval_id', $approval_id)->where('approval_level', $approval_level)->update(['level_status' => 1,'level_end_time'=>date('Y-m-d H:i:s',time())]);
    }

    /**
     * 更新下一级状态
     */
    public function updateNextLevel($approval_id, $approval_level)
    {
        return ApprovalUser::where('approval_id', $approval_id)->where('approval_level', $approval_level + 1)->update(['status' => 1]);
    }

    /**
     * 更新审批最终状态
     */
    public function updateEndStatus($approval_id, $data)
    {
        return Approval::find($approval_id)->update($data);
    }


    /**
     * 所有类型-所有状态
     */
    public function allAll($company_id)
    {
        return Approval::where('company_id', $company_id)->get();
    }

    /**
     * 部分类型-所有状态
     */
    public function partAll($type_id, $company_id)
    {
        return Approval::where('type_id', $type_id)->where('company_id', $company_id)->get();
    }

    /**
     * wo审批完成-所有类型-审批通过
     */
    public function allBy($company_id)
    {
        return Approval::where('end_status', 1)->where('company_id', $company_id)->get();
    }

    /**
     * wo审批完成-所有类型-审批不通过
     */
    public function allFail($company_id)
    {
        return Approval::where('end_status', 2)->where('company_id', $company_id)->get();
    }

    /**
     * wo审批完成-所有类型-审批中
     */
    public function allIng($company_id)
    {
        return Approval::where('end_status', 0)->where('company_id', $company_id)->get();
    }

    /**
     * wo审批完成-所有类型-审批已撤销
     */
    public function allCx($company_id)
    {
        return Approval::where('cancel_or_archive', 1)->where('company_id', $company_id)->get();
    }

    /**
     * wo审批完成-部分类型-审批通过
     */
    public function partBy($company_id, $type_id)
    {
        return Approval::where('end_status', 2)->where('type_id', $type_id)->where('company_id', $company_id)->get();
    }

    /**
     * wo审批完成-部分类型-审批不通过
     */
    public function partFail($company_id, $type_id)
    {
        return Approval::where('end_status', 1)->where('type_id', $type_id)->where('company_id', $company_id)->get();
    }

    /**
     * wo审批完成-部分类型-审批中
     */
    public function partIng($company_id, $type_id)
    {
        return Approval::where('end_status', 0)->where('type_id', $type_id)->where('company_id', $company_id)->get();
    }

    /**
     * wo审批完成-部分类型-已撤销
     */
    public function partCx($company_id, $type_id)
    {
        return Approval::where('cancel_or_archive', 1)->where('type_id', $type_id)->where('company_id', $company_id)->get();
    }


    /**
     * 我发起-所有类型-所有状态
     */
    public function initiatedAllAll($company_id, $applicant)
    {
        return Approval::where('company_id', $company_id)->where('applicant', $applicant)->get();
    }

    /**
     * 我发起-所有类型-审批通过的
     */
    public function initiatedAllBy($company_id, $applicant)
    {
        return Approval::where('end_status', 2)->where('company_id', $company_id)->where('applicant', $applicant)->get();
    }

    /**
     * 我发起-所有类型-审批不通过的
     */
    public function initiatedAllFail($company_id, $applicant)
    {
        return Approval::where('end_status', 2)->where('company_id', $company_id)->where('applicant', $applicant)->get();
    }

    /**
     * 我发起-所有类型-正在审批的
     */
    public function initiatedAllIng($company_id, $applicant)
    {
        return Approval::where('end_status', 0)->where('company_id', $company_id)->where('applicant', $applicant)->get();
    }

    /**
     * 我发起-所有类型-已撤销的
     */
    public function initiatedAllCx($company_id, $applicant)
    {
        return Approval::where('cancel_or_archive', 1)->where('company_id', $company_id)->where('applicant', $applicant)->get();
    }

    /**
     * 我发起-部分类型-所有状态
     */
    public function initiatedPartAll($company_id, $applicant, $type_id)
    {
        return Approval::where('type_id', $type_id)->where('company_id', $company_id)->where('applicant', $applicant)->get();
    }

    /**
     * 我发起-部分类型-审批通过的
     */
    public function initiatedPartEd($company_id, $applicant, $type_id)
    {
        return Approval::where('type_id', $type_id)->where('end_status', 2)->where('company_id', $company_id)->where('applicant', $applicant)->get();
    }

    /**
     * 我发起-部分类型-审批不通过的
     */
    public function initiatedPartFail($company_id, $applicant, $type_id)
    {
        return Approval::where('type_id', $type_id)->where('end_status', 1)->where('company_id', $company_id)->where('applicant', $applicant)->get();
    }

    /**
     * 我发起-部分类型-正在审批的
     */
    public function initiatedPartIng($company_id, $applicant, $type_id)
    {
        return Approval::where('type_id', $type_id)->where('end_status', 0)->where('company_id', $company_id)->where('applicant', $applicant)->get();
    }

    /**
     * 我发起-部分类型-已撤销的
     */
    public function initiatedPartCx($company_id, $applicant, $type_id)
    {
        return Approval::where('type_id', $type_id)->where('cancel_or_archive', 1)->where('company_id', $company_id)->where('applicant', $applicant)->get();
    }

    /**
     * 抄送给我的all
     */
    public function ccApprovalAll($company_id, $user_id)
    {
        return ApprovalCcMy::where('company_id', $company_id)->where('user_id', $user_id)->get();
    }

    /**
     * 抄送给我的part
     */
    public function ccApprovalPart($company_id, $user_id, $type_id)
    {
        return ApprovalCcMy::where('company_id', $company_id)->where('user_id', $user_id)->where('type_id', $type_id)->get();
    }

    /**
     * 已归档all
     */
    public function archivedAll($company_id, $user_id)
    {
        return Approval::where('company_id', $company_id)->where('applicant', $user_id)->where('cancel_or_archive', 2)->get();
    }

    /**
     * 已归档part
     */
    public function archivedPart($company_id, $user_id, $type_id)
    {
        return Approval::where('company_id', $company_id)->where('applicant', $user_id)->where('type_id', $type_id)->where('cancel_or_archive', 2)->get();
    }

    /**
     * 流程数据处理
     */
    public static function processData($process)
    {
        $data = [];
        foreach ($process as $key => $value) {
//            dd($value[0]->user,$key);
            $a = array_unique($value->pluck('status')->toArray());
            $approval_level = $key;
            $approval_type = $value[0]['type'];
            $level_end_time=$value[0]['level_end_time'];
            if ($value[0]['type'] == 'countersign') {
                if (count($a) == 1) {
                    $class_status = $a[0] == 0 ? '待接收' : ($a[0] == 1 ? '审批中' : ($a[0] == 2 ? '通过' : '不通过'));
                } else {
                    $class_status = in_array(3, $a) ? '不通过' : '审批中';
                }
            } elseif ($value[0]['type'] == 'orSign') {
                if (count($a) == 1) {
                    $class_status = $a[0] == 0 ? '待接收' : ($a[0] == 1 ? '审批中' : ($a[0] == 2 ? '通过' : '不通过'));
                } else {
                    $class_status = in_array(2, $a) ? '通过' : '审批中';
                }
            } else {
                $class_status = $a[0] == 0 ? '待接收' : ($a[0] == 1 ? '审批中' : ($a[0] == 2 ? '通过' : '不通过'));
            }
            $level_data = [];
            $user_data=[];
            foreach ($value as $v) {
                $transferee_user=null;
                $user = $v->user;
                $complete_time=$v->complete_time;
                $obj_transferee_user=User::find($v['transferee_id']);
                if($obj_transferee_user!=null){
                    $transferee_user=['user_name' => $obj_transferee_user->name,'user_id'=>$obj_transferee_user->id];
                }
                $status = $v['status'] == 0 ? '未接收' : ($v['status'] == 1 ? '审批中' : ($v['status'] == 2 ? '通过' : ($v['status'] == 3 ?'拒绝':'已转交')));
                $user_data[] = ['user_name' => $user->name,'user_id'=>$user->id, 'tel' => $user->tel, 'status' => $status,'opinion'=>$v->opinion, 'time'=>$complete_time,'transferee_user'=>$transferee_user];
            }
            $level_data=['approval_level' => $approval_level, 'approval_type' => $approval_type, 'class_status' => $class_status,'level_end_time'=>$level_end_time,'data'=>$user_data];
            $data[] = $level_data;
        }
        return $data;
    }

    /**
     * 评审发起人信息
     * @param $data
     */
    public static function sponsorData($data)
    {
        $user = User::find($data->applicant);
        $type = $data->name;
        $time = Carbon::parse($data->created_at)->toDateTimeString();
        return ['user_name' => $user->name, 'user_id'=>$user->id,'type' => $type, 'time' => $time];
    }

    /**
     * 判断按钮状态
     */
    public static function buttonStatus($data)
    {
        $approval_id = $data->id;
        $approver_id = auth('api')->user()->id;
        $approvalUser = ApprovalUser::where('approval_id', $approval_id)->where('approver_id', $approver_id)->first();
        $data = [];
        if ($approvalUser != null) {
            $data[] = ['status' => $approvalUser->status, 'level_status' => $approvalUser->level_status];
        }
        return $data;
    }

    /**
     * 判断我的身份和状态
     */
    public static function myIdentity($approval_id)
    {
        $user_id = auth('api')->user()->id;
        $approval = Approval::find($approval_id);
        $ccUserArray = $approval->cc->pluck('user_id')->toArray();//该申请对应的抄送
        $approvalUsers = ApprovalUser::where('approval_id', $approval_id)->where('approver_id', $user_id)->get();//审批人信息
        $sponsor = $user_id == $approval->applicant ? '是' : '否';//判断我是否是发起人
        $end_status =$approval->end_status;
        $cancel_or_archive=$approval->cancel_or_archive;
        $cc = in_array($user_id, $ccUserArray) ? '是' : '否';//判断我是否是被抄送人
        if(!empty($approvalUsers->toArray())){//判断是否是审批人
//            dd(empty($approvalUsers));
            $data =[];
            foreach ($approvalUsers as $v){
                $data[]=['status'=>$v->status,'level_status'=>$v->level_status];
            }
//            dd($data);
            foreach ($data as $v){
                if($v['level_status']==0&&$v['status']==1){
                    $approver=['是','审批中'];
                    break;//中止循环
                }elseif ($v['level_status']==1&&$v['status']==1){//该级结束我未审批判定为与我无关
                    $approver=['否'];
                }elseif ($v['status']==2){
                    $approver=['是','我已同意'];
                }elseif ($v['status']==3){
                    $approver=['是','我已拒绝'];
                }else{
                    $approver=['是','我未接收到'];
                }
            }
        }else{
            $approver =['否'];
        }
//        return [
//            'cancel_or_archive'=>$cancel_or_archive,//是否已归档或已撤销
//            'end_status'=>$end_status,//判断是否展示撤销或归档
//            'sponsor'=>$sponsor,//判断我是否是发起人
//            'cc'=>$cc,//判断我是否是被抄送人
//            'approver'=>$approver,//判断是否是审批人
//        ];
        if($cc=='是'&&$sponsor=='否'&&$approver==['否']){
            $buttons = [false,false,false,false,false,false,false,false,false,false];
            // return ['同意'=>'否','拒绝'=>'否','转交'=>'否','撤销'=>'否','归档'=>'否','催办'=>'否','重新提交'=>'否','导出Excel'=>'否','下载PDF'=>'否','打印'=>'否'];
        }elseif($cancel_or_archive==1&&$sponsor=='是'){//已撤销
            $buttons = [false,false,false,false,false,false,true,true,true,true];
            // return ['同意'=>'否','拒绝'=>'否','转交'=>'否','撤销'=>'否','归档'=>'否','催办'=>'否','重新提交'=>'是','导出Excel'=>'是','下载PDF'=>'是','打印'=>'是'];
        }elseif ($cancel_or_archive==1&&$sponsor=='否'){//已撤销
            $buttons = [false,false,false,false,false,false,false,true,true,true];
            // return ['同意'=>'否','拒绝'=>'否','转交'=>'否','撤销'=>'否','归档'=>'否','催办'=>'否','重新提交'=>'否','导出Excel'=>'是','下载PDF'=>'是','打印'=>'是'];
        }elseif ($cancel_or_archive==2){//已归档
            $buttons = [false,false,false,false,false,false,false,true,true,true];
//            return ['同意'=>'否','拒绝'=>'否','转交'=>'否','撤销'=>'否','归档'=>'否','催办'=>'否','重新提交'=>'否','导出Excel'=>'是','下载PDF'=>'是','打印'=>'是'];
        }elseif ($cancel_or_archive==0&&$end_status==0&&$sponsor=='是'&&$approver==['是','审批中']){//审批中
            $buttons = [true,true,true,true,false,true,false,true,true,true];
//            return ['同意'=>'是','拒绝'=>'是','转交'=>'是','撤销'=>'是','归档'=>'否','催办'=>'是','重新提交'=>'否','导出Excel'=>'是','下载PDF'=>'是','打印'=>'是'];
        }elseif ($cancel_or_archive==0&&$end_status==0&&$sponsor=='是'&&$approver==['是','我已同意']){//审批中
            $buttons = [false,false,false,true,false,true,false,true,true,true];
//            return ['同意'=>'否','拒绝'=>'否','转交'=>'否','撤销'=>'是','归档'=>'否','催办'=>'是','重新提交'=>'否','导出Excel'=>'是','下载PDF'=>'是','打印'=>'是'];
        }elseif ($cancel_or_archive==0&&$end_status==0&&$sponsor=='是'&&$approver==['是','我已拒绝']){//审批中
            $buttons = [false,false,false,true,false,true,true,true,true,true];
//            return ['同意'=>'否','拒绝'=>'否','转交'=>'否','撤销'=>'是','归档'=>'否','催办'=>'是','重新提交'=>'否','导出Excel'=>'是','下载PDF'=>'是','打印'=>'是'];
        }elseif ($cancel_or_archive==0&&$end_status==0&&$sponsor=='是'&&$approver==['否']){//审批中
            $buttons = [false,false,false,true,false,true,false,true,true,true];
//            return ['同意'=>'否','拒绝'=>'否','转交'=>'否','撤销'=>'是','归档'=>'否','催办'=>'是','重新提交'=>'否','导出Excel'=>'是','下载PDF'=>'是','打印'=>'是'];
        }elseif ($cancel_or_archive==0&&$end_status!=0&&$sponsor=='是'){//审批完成
            $buttons = [false,false,false,false,true,false,true,true,true,true];
//            return ['同意'=>'否','拒绝'=>'否','转交'=>'否','撤销'=>'否','归档'=>'是','催办'=>'否','重新提交'=>'否','导出Excel'=>'是','下载PDF'=>'是','打印'=>'是'];
        }
        elseif ($cancel_or_archive==0&&$end_status==0&&$sponsor=='否'&&$approver==['是','审批中']){//审批中
            $buttons = [true,true,true,false,false,false,false,true,true,true];
//            return ['同意'=>'是','拒绝'=>'是','转交'=>'是','撤销'=>'否','归档'=>'否','催办'=>'否','重新提交'=>'否','导出Excel'=>'是','下载PDF'=>'是','打印'=>'是'];
        }elseif ($cancel_or_archive==0&&$end_status==0&&$sponsor=='否'&&$approver==['是','我已同意']){//审批中
            $buttons = [false,false,false,false,false,false,false,true,true,true];
//            return ['同意'=>'否','拒绝'=>'否','转交'=>'否','撤销'=>'否','归档'=>'否','催办'=>'否','重新提交'=>'否','导出Excel'=>'是','下载PDF'=>'是','打印'=>'是'];
        }elseif ($cancel_or_archive==0&&$end_status==0&&$sponsor=='否'&&$approver==['是','我已拒绝']){//审批中
            $buttons = [false,false,false,false,false,false,false,true,true,true];
//            return ['同意'=>'否','拒绝'=>'否','转交'=>'否','撤销'=>'否','归档'=>'否','催办'=>'否','重新提交'=>'否','导出Excel'=>'是','下载PDF'=>'是','打印'=>'是'];
        }elseif ($cancel_or_archive==0&&$end_status==0&&$sponsor=='否'&&$approver==['否']){//审批中
            $buttons = [false,false,false,false,false,false,false,true,true,true];
//            return ['同意'=>'否','拒绝'=>'否','转交'=>'否','撤销'=>'否','归档'=>'否','催办'=>'否','重新提交'=>'否','导出Excel'=>'是','下载PDF'=>'是','打印'=>'是'];
        }elseif ($cancel_or_archive==0&&$end_status!=0&&$sponsor=='否'){//审批完成
            $buttons = [false,false,false,false,false,false,false,true,true,true];
//            return ['同意'=>'否','拒绝'=>'否','转交'=>'否','撤销'=>'否','归档'=>'否','催办'=>'否','重新提交'=>'否','导出Excel'=>'是','下载PDF'=>'是','打印'=>'是'];
        }else{
            $buttons = [false,false,false,false,false,false,false,false,false,false];
//            return ['同意'=>'否','拒绝'=>'否','转交'=>'否','撤销'=>'否','归档'=>'否','催办'=>'否','重新提交'=>'否','导出Excel'=>'否','下载PDF'=>'否','打印'=>'否'];
        }
        return [
            [
                'title' => '同意',
                'permission'=> $buttons[0]
            ],
            [
                'title' => '拒绝',
                'permission'=> $buttons[1]
            ],
            [
                'title' => '转交',
                'permission'=> $buttons[2]
            ],
            [
                'title' => '撤销',
                'permission'=> $buttons[3]
            ],
            [
                'title' => '归档',
                'permission'=> $buttons[4]
            ],
            [
                'title' => '催办',
                'permission'=> $buttons[5]
            ],
            [
                'title' => '再次申请',
                'permission'=> $buttons[6]
            ],
            [
                'title' => '导出Excel',
                'permission'=> $buttons[7]
            ],
            [
                'title' => '下载PDF',
                'permission'=> $buttons[8]
            ],
            [
                'title' => '预览',
                'permission'=> $buttons[9]
            ],
        ];
    }

    /**
     * 判断模板的可用范围
     */
    public static function ableRange($array)
    {
        if($array==null){
            return '全体员工';
        }else{
            $depart_id_array= FunctionTool::decrypt_id_array(json_decode($array)->departmentId);//被限定的部门id
            $staff_id_array= FunctionTool::decrypt_id_array(json_decode($array)->staffId);//被限定的员工id
            $depart_names=Department::whereIn('id',$depart_id_array)->pluck('name');
            $staff_names=User::whereIn('id',$staff_id_array)->pluck('name');
            $data=['depart_names'=>$depart_names,'staff_names'=>$staff_names];
            return $data;
        }
    }
    /**
     * 返回拥有模板使用权限的所有用户id
     */
    public function tem_per_staff_ids($temId,$departmentTools)
    {
        $array=ApprovalTemplate::find($temId)->per;
        $depart_id_array= json_decode($array)->department_id;//被限定的部门id
        $staff_id_array= json_decode($array)->staff_id;//被限定的员工id
        $staff_ids=[];
        if($depart_id_array!=null){
            foreach ($depart_id_array as $v){
                $staff_ids=array_merge($staff_ids,$departmentTools->getNodeUsers($v));//调用部门工具类中getNodeUsers获取部门下的所有用户id
            }
        }
        $tem_per_staff_ids=array_unique(array_merge($staff_ids,$staff_id_array));//可以使用此模板的用户id
        return $tem_per_staff_ids;
    }

}