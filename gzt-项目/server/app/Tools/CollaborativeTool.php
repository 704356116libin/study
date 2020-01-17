<?php

namespace App\Tools;

use App\Http\Resources\TaskResource;
use App\Interfaces\CollaborativeInterface;
use App\Models\CollaborationInvitation;
use App\Models\CollaborativeTask;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Repositories\CollaborativeRepository;
use App\Events\CollaborationInvitationEvent;
use App\Events\News;
use App\Repositories\OssFileRepository;

class CollaborativeTool implements CollaborativeInterface
{
    static private $collaborativeTool;
    public $collaborativeRepository;
    public $companyNoticeTool;
    public $ossFileRepository;

    /**
     * 构造函数
     * CollaborativeTool constructor.
     */
    public function __construct()
    {
        $this->ossFileRepository = OssFileRepository::getOssFileRepository();
        $this->companyNoticeTool = CompanyNoticeTool::getCompanyNoticeTool();
        $this->collaborativeRepository = CollaborativeRepository::getCollaborativeRepository();
    }

    /**
     * 单例模式
     * @return CollaborativeTool
     */
    static public function getCollaborativeTool()
    {
        if (self::$collaborativeTool instanceof self) {
            return self::$collaborativeTool;
        } else {
            return self::$collaborativeTool = new self();
        }
    }

    /**
     * 防止克隆
     */
    private function _clone()
    { }

    /**
     * 邀请者发起协作任务并邀请好友或公司人员
     * 触发邀请事件,执行对应监听器下的handle方法中的代码
     */
    public function sendInvite($request)
    {
        DB::beginTransaction();
        try {
            $files = $_FILES;
            //负责人id
            $pst_id = array_get($request, 'pst_id');
            $principalId = FunctionTool::decrypt_id(json_decode($request['principalId'], true)['key']);
            //参与人ids
            $participantsIds = $this->participantsIds(json_decode($request['participantsId'], true)['checkedPersonnels']);
            $internal = $participantsIds['internal']; //内部参与人ids
            $partners = $participantsIds['partners']; //合作伙伴参与人ids
            $external = $participantsIds['external']; //外部参与人ids

            //创建任务,入数据库
            $user = auth('api')->user();
            $formEdit = json_decode($request['formEdit']);
            $formPeople = json_decode($request['formPeople'], true);
            if ($formEdit) {
                if (count($formPeople) == 1) {
                    if ($formPeople[0] == '参与人') {
                        $edit_form = 2;
                    } else {
                        $edit_form = 1;
                    }
                } elseif (count($formPeople) == 0) { //都不能编辑
                    $edit_form = 3;
                } else { //都能编辑
                    $edit_form = 0;
                }
            } else {
                $edit_form = 3;
            }
            if ($user->id == $principalId) {
                $is_receive = 1;
            } else {
                $is_receive = 0;
            }
            $task_data = [
                'title' => $request['title'],
                'description' => $request['description'],
                //                'form_area' => json_encode($request['formArea'])==null?null:json_encode($request['formArea']),
                'initiate_id' => $user->id,
                'principal_id' => $principalId,
                'limit_time' => $request['limitTime'] == 'unlimited' ? null : $request['limitTime'],
                'edit_form' => $edit_form,
                'form_edit' => $formEdit,
                'form_people' => $formEdit == true ? $request['formPeople'] : null,
                'participants' => $request['participantsId'], //参与人组织结构树
                'company_id' => $user->current_company_id,
                'is_receive' => $is_receive,
                'pst_id' => $pst_id,
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time()),
            ];
            $task = CollaborativeTask::create($task_data);
            $task_id = $task->id;
            //添加任务与邀请人,被邀请人的对应关系
            $invita_data = [
                'initiate_user' => $user->id,
                'collaborative_task_id' => $task_id,
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time()),
            ];
            //            dd($request['participantsId']);
            if ($participantsIds != []) {
                $internal_data = []; //内部参与人ids
                $partners_data = []; //合作伙伴参与人ids
                $external_data = []; //外部参与人ids
                //内部参与人
                foreach ($internal as $user_id) {
                    if ($user_id == $user->id) { //如果参与人id,是发起人或负责人,则该参与人状态为已接受
                        $invita_data['status'] = 1;
                    } else {
                        $invita_data['status'] = 3;
                    }
                    $invita_data['company_id'] = $user->current_company_id;
                    $invita_data['receive_user'] = $user_id;
                    $invita_data['type'] = '内部人员';
                    $internal_data[] = $invita_data;
                }
                //合作伙伴参与人
                foreach ($partners as $user_id) {
                    if ($user_id['creator_id'] == $user->id) { //如果参与人id,是发起人或负责人,则该参与人状态为已接受
                        $invita_data['status'] = 1;
                    } else {
                        $invita_data['status'] = 3;
                    }
                    $invita_data['type'] = '合作伙伴';
                    $invita_data['company_id'] = $user_id['id'];
                    $invita_data['receive_user'] = $user_id['creator_id'];
                    $partners_data[] = $invita_data;
                }
                //外部参与人
                foreach ($external as $user_id) {
                    if ($user_id == $user->id) { //如果参与人id,是发起人或负责人,则该参与人状态为已接受
                        $invita_data['status'] = 1;
                    } else {
                        $invita_data['status'] = 3;
                    }
                    $invita_data['type'] = '外部联系人';
                    $invita_data['company_id'] = 0;
                    $invita_data['receive_user'] = $user_id;
                    $external_data[] = $invita_data;
                }
                $invita_datas = array_merge($internal_data, $partners_data, $external_data);
                DB::table('collaboration_invitation')->insert($invita_datas); //插入被邀其协助人信息
            }
            /**
             * 上传文件
             */
            $company = Company::find($user->current_company_id);
            $data = true;
            if (count($files) != 0) {
                $data = CompanyOssTool::uploadFile($files, [
                    'oss_path' => $company->oss->root_path . 'collaboration', //文件存入的所在目录
                    'model_id' => $task_id, //关联模型id
                    'model_type' => CollaborativeTask::class, //关联模型类名
                    'company_id' => $user->current_company_id, //所属公司id
                    'uploader_id' => $user->id, //上传者id
                ]);
            }
            if ($request['deletefilesId'] != []) {
                $this->ossFileRepository->removeFileRelation(json_decode($request['deletefilesId'], true), ['model_id' => $task_id, 'model_type' => CollaborativeTask::class]); //删除附件(关系)
            }
            if ($request['notification_way'] != null) {
                /**
                 * 通知
                 */
                //组装动态列表单个数据格式
                //                $single_data = DynamicTool::getSingleListData(CollaborativeTask::class, 1, 'company_id', $company->id,
                //                    $company->name, '协助邀请:' . $task->user->name . '邀请你参与' . $task->title . '协助', $task->created_at);
                //                $user_ids[] = $principalId;//被通知人ids
                //                NotifyTool::publishNotify(array_unique($user_ids), $user->current_company_id, $task, $request['notification_way'], $single_data, []);//此处方法顺序待调整
            }
            if ($data) {
                DB::commit();
                return ['status' => 'success', 'message' => '协助任务创建成功'];
            } else {
                DB::rollBack();
                return ['status' => 'success', 'message' => '附件上传失败!,协助任务创建失败!' . $data];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return ['status' => 'fail', 'message' => '服务器错误'];
        }
    }

    /**
     * 抽取组织结构树,合作伙伴,外部联系人,中参与人的id
     */
    private function participantsIds($checkedPersonnels)
    {
        $organizational = $checkedPersonnels['organizational'];
        $partner = $checkedPersonnels['partner'];
        $externalContact = $checkedPersonnels['externalContact'];
        $internal = [];
        $external = [];
        //内部人员参与者的id
        foreach ($organizational as $v) {
            $internal[] = $v['key'];
        }
        //外部联系人参与者的id
        foreach ($externalContact as $v) {
            $external[] = $v['key'];
        }
        //合作伙伴公司负责人的ids
        $partnerIds = [];
        foreach ($partner as $v) {
            $partnerIds[] = $v['key'];
        }
        $partnerIds = FunctionTool::decrypt_id_array(array_unique($partnerIds));
        $partners = DB::table('company')->whereIn('id', $partnerIds)->get()->map(function ($company) {
            return [
                'creator_id' => $company->creator_id,
                'id' => $company->id,
            ];
        })->toArray();
        return ['internal' => FunctionTool::decrypt_id_array($internal), 'partners' => $partners, 'external' => FunctionTool::decrypt_id_array($external)];
    }
    /**
     * 编辑任务
     */
    public function editTask(Request $request)
    {
        $files = $_FILES;
        $formPeople = json_decode($request->formPeople, true);
        // 负责人id
        $principalId = json_decode($request['principalId'], true)['key'];
        $task_id = FunctionTool::decrypt_id($request->id);
        $task = CollaborativeTask::find($task_id);
        DB::beginTransaction();
        try {
            //参与人ids
            $participantsIds = $this->participantsIds(json_decode($request['participantsId'], true)['checkedPersonnels']);
            $internal = $participantsIds['internal']; //内部参与人ids
            $partners = $participantsIds['partners']; //合作伙伴参与人ids
            $external = $participantsIds['external']; //外部参与人ids
            //创建任务,入数据库
            $user = auth('api')->user();
            $formEdit = json_decode($request->formEdit, true);
            if ($formEdit) {
                if (count($formPeople) == 1) {
                    if ($formPeople[0] == '参与人') {
                        $edit_form = 2;
                    } else {
                        $edit_form = 1;
                    }
                } elseif (count($formPeople) == 0) { //都不能编辑
                    $edit_form = 3;
                } else { //都能编辑
                    $edit_form = 0;
                }
            } else {
                $edit_form = 3;
            }
            if ($user->id == $principalId) {
                $is_receive = 1;
            } else {
                $is_receive = 0;
            }
            $task_data = [
                'title' => $request->title,
                'description' => $request->description,
                'form_area' => json_encode($request->formArea),
                'principal_id' => $principalId,
                'limit_time' => $request->limitTime == 'unlimited' ? null : $request->limitTime,
                'edit_form' => $edit_form,
                'is_receive' => $is_receive,
                'form_edit' => $formEdit,
                'form_people' => $formEdit == true ? $request->formPeople : null,
                'company_id' => $user->current_company_id,
                'participants' => $request['participantsId'], //参与人组织结构树
                //                'pst_id'=>$pst_id,//评审通id编辑时不需要
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time()),
            ];
            CollaborativeTask::where('id', $task_id)->update($task_data); //修改任务内容
            CollaborationInvitation::where('collaborative_task_id', $task_id)->delete(); //删除该任务协助者关系
            //添加任务与邀请人,被邀请人的对应关系
            $invita_data = [
                'initiate_user' => $user->id,
                'collaborative_task_id' => $task_id,
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time()),
            ];
            //            dd($request['participantsId']);
            if ($participantsIds != []) {
                $internal_data = []; //内部参与人ids
                $partners_data = []; //合作伙伴参与人ids
                $external_data = []; //外部参与人ids
                //内部参与人
                foreach ($internal as $user_id) {
                    if ($user_id == $user->id) { //如果参与人id,是发起人或负责人,则该参与人状态为已接受
                        $invita_data['status'] = 1;
                    } else {
                        $invita_data['status'] = 3;
                    }
                    $invita_data['company_id'] = $user->current_company_id;
                    $invita_data['receive_user'] = $user_id;
                    $internal_data[] = $invita_data;
                }
                //合作伙伴参与人
                foreach ($partners as $user_id) {
                    if ($user_id['creator_id'] == $user->id) { //如果参与人id,是发起人或负责人,则该参与人状态为已接受
                        $invita_data['status'] = 1;
                    } else {
                        $invita_data['status'] = 3;
                    }
                    $invita_data['company_id'] = $user_id['id'];
                    $invita_data['receive_user'] = $user_id['creator_id'];
                    $partners_data[] = $invita_data;
                }
                //外部参与人
                foreach ($external as $user_id) {
                    if ($user_id == $user->id) { //如果参与人id,是发起人或负责人,则该参与人状态为已接受
                        $invita_data['status'] = 1;
                    } else {
                        $invita_data['status'] = 3;
                    }
                    $invita_data['company_id'] = 0;
                    $invita_data['receive_user'] = $user_id;
                    $external_data[] = $invita_data;
                }
                $invita_datas = array_merge($internal_data, $partners_data, $external_data);
                DB::table('collaboration_invitation')->insert($invita_datas); //插入被邀其协助人信息
            }
            $this->deleteFile($request); //删除附件关系
            /**
             * 上传文件
             */
            $company = Company::find($user->current_company_id);
            $uploadFileStatus = true;
            if (count($files) != 0) {
                $uploadFileStatus = CompanyOssTool::uploadFile($files, [
                    'oss_path' => $company->oss->root_path . 'collaboration', //文件存入的所在目录
                    'model_id' => FunctionTool::decrypt_id($request->id), //关联模型id
                    'model_type' => CollaborativeTask::class, //关联模型类名
                    'company_id' => $user->current_company_id, //所属公司id
                    'uploader_id' => $user->id, //上传者id
                ]);
            }
            $notification_way = json_decode($request->notification_way, true);
            if ($notification_way != null) {
                /**
                 * 通知
                 */
                //组装动态列表单个数据格式
                $single_data = DynamicTool::getSingleListData(
                    CollaborativeTask::class,
                    1,
                    'company_id',
                    $company->id,
                    $company->name,
                    '协助邀请:' . $task->user->name . '邀请你参与' . $task->title . '协助',
                    $task->created_at
                );
                $user_ids[] = $principalId; //被通知人ids

                NotifyTool::publishNotify(array_unique($user_ids), $user->current_company_id, $task, $notification_way, $single_data, []); //此处方法顺序待调整
            }
            if ($uploadFileStatus) {
                DB::commit();
                return json_encode(['status' => 'success', 'message' => '协助任务编辑成功']);
            } else {
                DB::rollBack();
                return json_encode(['status' => 'fail', 'message' => '协助任务编辑失败，附件' . $uploadFileStatus]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            return ['status' => 'error', 'message' => '服务器错误'];
        }
    }


    /**
     * 根据任务id 获取任务详情
     * @param $task_id
     * @return TaskResource
     */
    public function taskDetail($task_id)
    {
        $task_id = FunctionTool::decrypt_id($task_id);
        $detial = new TaskResource(CollaborativeTask::find($task_id));
        return json_encode($detial);
    }

    /**
     * 资源类处理
     * @param Request $request
     */
    public function dealTaskList(Request $request)
    {
        $internalOrExternal = $request->internalOrExternal;
        if ($internalOrExternal !== 'cross') { //不等于外部(全部或内部是)
            $data = [];
            $taskData = json_decode($this->taskList($request), true);
            $all_count = count($taskData);
            $taskList = array_slice($taskData, $request->offset, $request->limit);
            if ($request->type == 'involved') {
                foreach ($taskList as $value) {
                    $status = $this->left_s($value['task']);
                    $data[] = [
                        'id' => FunctionTool::encrypt_id($value['task']['id']),
                        'title' => $value['task']['title'],
                        'initiate' => ['initiate_id' => $value['task']['initiate_id'], 'initiate_name' => User::find($value['task']['initiate_id'])->name],
                        'description' => $value['task']['description'],
                        'created_at' => $value['task']['created_at'],
                        'limit_time' => $value['task']['limit_time'],
                        'status' => $status,
                        'is_cancel' => $value['task']['is_delete'],
                    ];
                }
            } else {
                foreach ($taskList as $value) {
                    $status = $this->left_s($value);
                    $data[] = [
                        'id' => FunctionTool::encrypt_id($value['id']),
                        'title' => $value['title'],
                        'initiate' => ['initiate_id' => $value['initiate_id'], 'initiate_name' => User::find($value['initiate_id'])->name],
                        'description' => $value['description'],
                        'created_at' => $value['created_at'],
                        'limit_time' => $value['limit_time'],
                        'status' => $status,
                        'is_cancel' => $value['is_delete'],
                    ];
                }
            }
            if ($request->type == 'all' && ($internalOrExternal === 'all' || $internalOrExternal === null)) {
                $wb = json_decode($this->partner_assistance($request), true);
                $data = array_slice(array_merge($wb['data'], $data), $request->offset, $request->limit);
                $all_count = $all_count + $wb['all_count'];
                return $data = ['all_count' => $all_count, 'data' => $data];
            }
            $data = ['all_count' => $all_count, 'data' => $data];
            return json_encode($data);
        } else { //等于外部是
            return $this->partner_assistance($request);
        }
    }

    /**
     * 处理搜索查询
     */
    public function search(Request $request)
    {
        $data = [];
        $s_search = json_decode($this->s_search($request->status, $request->title, $request->offset, $request->limit));
        $all_count = count($s_search);
        $s_search = array_slice($s_search, $request->offset, $request->limit);
        foreach ($s_search as $value) {
            $status = $this->collaborativeRepository->left_s($value);
            $data[] = [
                'id' => $value->id,
                'title' => $value->title,
                'description' => $value->description,
                'created_at' => $value->created_at,
                'limit_time' => $value->limit_time,
                'status' => $status,
                'is_cancel' => $value->is_delete,
            ];
        }
        $data = ['all_count' => $all_count, 'data' => $data];
        return json_encode($data);
    }

    /**
     * 点击接受按钮
     */
    public function receiveButton(Request $request)
    {
        try {
            $id = FunctionTool::decrypt_id($request->id);
            $identy = $request->identy;
            $user = auth('api')->user();
            $receive = $this->collaborativeRepository->receiveButton($id, $identy, $user->id);
            if ($receive == '已接受') {
                if ($request->notification_way != null) {
                    /**
                     * 通知
                     */
                    //组装动态列表单个数据格式
                    $content = $user->id->name . '接收了您的邀请'; //通知内容
                    $task = CollaborativeTask::find($id);
                    $user_ids = [$task->initiate_id]; //被通知人id
                    $this->collaborative_notify(['task' => $task, 'content' => $content, 'notification_way' => $request->notification_way, 'user_ids' => $user_ids]);
                }
                return json_encode(['status' => 'success', 'message' => '操作重复']);
            } elseif ($receive) {
                return json_encode(['status' => 'success', 'message' => '操作成功']);
            } else {
                return json_encode(['status' => 'fail', 'message' => '操作失败']);
            }
        } catch (\Exception $e) {
            return ['status' => 'fail', 'message' => '服务器错误'];
        }
    }

    /**
     * 点击转交按钮
     */
    public function transferButton(Request $request)
    {
        $user = \auth('api')->user();
        $collaborative_task_id = FunctionTool::decrypt_id($request->collaborative_task_id); //协助任务的id
        if ($request->type == '内部人员') {
            $transferred_person=CollaborationInvitation::where('collaborative_task_id', $collaborative_task_id)->value('transferred_person');
            if($transferred_person!==null){
                return ['status'=>'fail','message'=>'已转交等待确定'];
            }
            $transfer_reason = $request->transfer_reason; //转交缘由
            $transferred_person = FunctionTool::decrypt_id($request->transferred_person); //被转交人id
            $receive_user = $user->id; //参与人id
            $count = CollaborationInvitation::where('collaborative_task_id', $collaborative_task_id)
                ->where('receive_user', $receive_user)
                ->where('status', 1)
                ->update(['transferred_person' => $transferred_person, 'transfer_reason' => $transfer_reason, 'replace_company_id' => $user->current_company_id]);
            if ($count > 0) {
                return ['status' => 'success', 'message' => '操作成功'];
            } else {
                return ['status' => 'fail', 'message' => '操作失败'];
            }
        } elseif ($request->type == '合作伙伴') {
            $replace_company_id=CollaborationInvitation::where('collaborative_task_id', $collaborative_task_id)->value('replace_company_id');
            if($replace_company_id!==null){
                return ['status'=>'fail','message'=>'已转交等待确定'];
            }
            $company_id = FunctionTool::decrypt_id($request->company_id); //合作公司id(加密)
            $company = Company::find($company_id);
            $transfer_reason = $request->transfer_reason; //转交缘由
            $transferred_person = $company->creator_id; //被转交人id
            $receive_user = $user->id; //参与人id
            $count = CollaborationInvitation::where('collaborative_task_id', $collaborative_task_id)
                ->where('receive_user', $receive_user)
                ->where('status', 1)
                ->update(['transferred_person' => $transferred_person, 'transfer_reason' => $transfer_reason, 'company_id' => $company->id, 'replace_company_id' => $company_id]);
            if ($count > 0) {
                return ['status' => 'success', 'message' => '操作成功'];
            } else {
                return ['status' => 'fail', 'message' => '操作失败'];
            }
        } elseif ($request->type == '外部联系人') {
            $transferred_person=CollaborationInvitation::where('collaborative_task_id', $collaborative_task_id)->value('transferred_person');
            if($transferred_person!==null){
                return ['status'=>'fail','message'=>'已转交等待确定'];
            }
            $transfer_reason = $request->transfer_reason; //转交缘由
            $transferred_person = FunctionTool::decrypt_id($request->transferred_person); //被转交人id
            $receive_user = $user->id; //参与人id
            $count = CollaborationInvitation::where('collaborative_task_id', $collaborative_task_id)
                ->where('receive_user', $receive_user)
                ->where('status', 1)
                ->update(['transferred_person' => $transferred_person, 'transfer_reason' => $transfer_reason, 'replace_company_id' => 0]);
            if ($count > 0) {
                return ['status' => 'success', 'message' => '操作成功'];
            } else {
                return ['status' => 'fail', 'message' => '操作失败'];
            }
        } elseif ($request->type == '负责人') { //负责人转交
            $zj_principal_id=CollaborativeTask::where('id', $collaborative_task_id)->value('zj_principal_id');
            if($zj_principal_id!==null){
                return ['status'=>'fail','message'=>'已转交等待确定'];
            }
            $zj_reason = $request->transfer_reason; //转交缘由
            $zj_principal_id = FunctionTool::decrypt_id($request->transferred_person); //被转交人id
            $principal_id = $user->id; //参与人id(及负责人身份)
            $count = CollaborativeTask::where('id', $collaborative_task_id)
                ->where('principal_id', $principal_id)
                ->update(['zj_principal_id' => $zj_principal_id, 'zj_reason' => $zj_reason]);
            if ($count > 0) {
                return ['status' => 'success', 'message' => '操作成功'];
            } else {
                return ['status' => 'fail', 'message' => '操作失败'];
            }
        }
    }

    /**
     * 转交列表
     */
    public function transferList()
    {
        $user = auth('api')->user();
        $cyr = CollaborationInvitation::where('transferred_person', $user->id)->get()->map(function ($cyr) {
            return [
                'task_id' => $cyr->task->id,
                'title' => $cyr->task->title,
                'user_name' => User::find($cyr->receive_user)->name,
                'type' => '参与人',
            ];
        })->toarray(); //参与人
        $fzr = CollaborativeTask::where('zj_principal_id', $user->id)->get()->map(function ($fzr) {
            return [
                'task_id' => $fzr->id,
                'title' => $fzr->title,
                'user_name' => User::find($fzr->principal_id)->name,
                'type' => '负责人',
            ];
        })->toarray(); //负责人
        return array_merge($cyr, $fzr);
    }

    /**
     * 操作(接收转交任务,拒绝转交任务)
     */
    public function transferOperating($request)
    {
        $user = auth('api')->user();
        $type = $request->type;
        $id = $request->id;
        $operate = $request->operate;
        if ($type == '负责人') {
            if ($operate == 'agree') {
                $task = CollaborativeTask::find($id);
                $count = CollaborativeTask::where('id', $id)
                    ->update(['principal_id' => $task->zj_principal_id, 'zj_reason' => null, 'zj_principal_id' => null]);
                if ($count > 0) {
                    return ['status' => 'success', 'message' => '操作成功'];
                } else {
                    return ['status' => 'fail', 'message' => '操作失败'];
                }
            } elseif ($operate == 'refuse') {
                $count = CollaborativeTask::where('id', $id)
                    ->update(['zj_reason' => null, 'zj_principal_id' => null]);
                if ($count > 0) {
                    return ['status' => 'success', 'message' => '操作成功'];
                } else {
                    return ['status' => 'fail', 'message' => '操作失败'];
                }
            }
        }
        if ($operate == 'agree') {
            $collabora = CollaborationInvitation::find($id);
            $count = CollaborationInvitation::where('id', $id)
                ->update(['receive_user' => $user->id, 'transferred_person' => null, 'transfer_reason' => null, 'replace_company_id' => null, 'company_id' => $collabora->replace_company_id]);
            if ($count > 0) {
                return ['status' => 'success', 'message' => '操作成功'];
            } else {
                return ['status' => 'fail', 'message' => '操作失败'];
            }
        } elseif ($operate == 'refuse') {
            $count = CollaborationInvitation::where('id', $id)
                ->update(['transferred_person' => null, 'transfer_reason' => null, 'replace_company_id' => null]);
            if ($count > 0) {
                return ['status' => 'success', 'message' => '操作成功'];
            } else {
                return ['status' => 'fail', 'message' => '操作失败'];
            }
        }
    }

    /**
     * 点击拒绝按钮
     */
    public function rejectButton(Request $request)
    {
        try {
            $id = FunctionTool::decrypt_id($request->id);
            $identy = $request->identy;
            $user = auth('api')->user();
            $receive = $this->collaborativeRepository->rejectButton($id, $identy, $user->id);
            if ($receive == '已拒绝') {
                if ($request->notification_way != null) {
                    /**
                     * 通知
                     */
                    //组装动态列表单个数据格式
                    $content = $user->id->name . '拒绝了您的邀请'; //通知内容
                    $task = CollaborativeTask::find($id);
                    $user_ids = [$task->initiate_id]; //被通知人id
                    $this->collaborative_notify(['task' => $task, 'content' => $content, 'notification_way' => $request->notification_way, 'user_ids' => $user_ids]);
                }
                return json_encode(['status' => 'success', 'message' => '操作重复']);
            } elseif ($receive) {
                return json_encode(['status' => 'success', 'message' => '操作成功']);
            } else {
                return json_encode(['status' => 'fail', 'message' => '操作失败']);
            }
        } catch (\Exception $e) {
            return ['status' => 'fail', 'message' => '服务器错误'];
        }
    }

    /**
     * 点击完成按钮
     */
    public function carryOutButton(Request $request)
    {
        try {
            $id = FunctionTool::decrypt_id($request->id);
            $identy = $request->identy;
            $opinion = $request->opinion;
            $user = auth('api')->user();
            $receive = $this->collaborativeRepository->carryOutButton($id, $identy, $user->id, $opinion);
            if ($receive == '已完成') {
                if ($request->notification_way != null) {
                    /**
                     * 通知
                     */
                    //组装动态列表单个数据格式
                    $content = $user->id->name . '的任务已完成'; //通知内容
                    $task = CollaborativeTask::find($id);
                    $user_ids = [$task->initiate_id, $task->principal_id]; //被通知人id
                    $this->collaborative_notify(['task' => $task, 'content' => $content, 'notification_way' => $request->notification_way, 'user_ids' => $user_ids]);
                }
                return json_encode(['status' => 'success', 'message' => '操作重复']);
            } elseif ($receive) {
                return json_encode(['status' => 'success', 'message' => '操作成功']);
            } else {
                return json_encode(['status' => 'fail', 'message' => '操作失败']);
            }
        } catch (\Exception $e) {
            return ['status' => 'fail', 'message' => '服务器错误'];
        }
    }

    /**
     * 发起人审核操作
     * @param Request $request
     */
    public function auditButton(Request $request)
    {
        try {
            $id = FunctionTool::decrypt_id($request->id);
            $is_agree = $request->isAgree;
            $opinion = $request->opinion;
            $is = $this->collaborativeRepository->auditButton($id, $is_agree, $opinion);
            if ($is == '该任务已是完成状态') {
                return json_encode(['status' => 'success', 'message' => '操作重复,该任务已经是完成状态']);
            } elseif ($is) {
                if ($request->notification_way != null) {
                    /**
                     * 通知
                     */
                    //组装动态列表单个数据格式
                    $task = CollaborativeTask::find($id);
                    $content = $task->title . '任务审核'; //通知内容
                    $user_ids = [$task->principal_id]; //被通知人id
                    $this->collaborative_notify(['task' => $task, 'content' => $content, 'notification_way' => $request->notification_way, 'user_ids' => $user_ids]);
                }
                return json_encode(['status' => 'success', 'message' => '操作成功']);
            } else {
                return json_encode(['status' => 'fail', 'message' => '操作失败']);
            }
        } catch (\Exception $e) {
            return ['status' => 'fail', 'message' => '服务器错误'];
        }
    }

    /**
     * 发起人撤销
     * @param Request $request
     * @return mixed
     */
    public function cancel(Request $request)
    {
        try {
            $id = FunctionTool::decrypt_id($request->id);
            $user = auth('api')->user();
            $cancel = $this->collaborativeRepository->cancel($id, $user->id, $request->initiate_opinion);
            if ($cancel) {
                if ($request->notification_way != null) {
                    /**
                     * 通知
                     */
                    //组装动态列表单个数据格式
                    $task = CollaborativeTask::find($id);
                    $content = $task->title . '任务已撤销'; //通知内容
                    $user_ids = $task->invitations->pluck('receive_user')->toarray(); //被通知人id
                    $user_ids[] = $task->principal_id;
                    $this->collaborative_notify(['task' => $task, 'content' => $content, 'notification_way' => $request->notification_way, 'user_ids' => $user_ids]);
                }
                return json_encode(['status' => 'success', 'message' => '操作成功']);
            } else {
                return json_encode(['status' => 'fail', 'message' => '操作失败,请确认是否是该任务发起人']);
            }
        } catch (\Exception $e) {
            return ['status' => 'fail', 'message' => '服务器错误'];
        }
    }

    /**
     * 恢复任务
     */
    public function recoveryTask(Request $request)
    {
        try {
            $id = FunctionTool::decrypt_id($request->id);
            $user = auth('api')->user();
            $cancel = $this->collaborativeRepository->recoveryTask($id, $user->id);
            if ($cancel) {
                if ($request->notification_way != null) {
                    /**
                     * 通知
                     */
                    //组装动态列表单个数据格式
                    $task = CollaborativeTask::find($id);
                    $content = $task->title . '任务已恢复'; //通知内容
                    $user_ids = $task->invitations->pluck('receive_user')->toarray(); //被通知人id
                    $user_ids[] = $task->principal_id;
                    $this->collaborative_notify(['task' => $task, 'content' => $content, 'notification_way' => $request->notification_way, 'user_ids' => $user_ids]);
                }
                return json_encode(['status' => 'success', 'message' => '操作成功']);
            } else {
                return json_encode(['status' => 'fail', 'message' => '操作失败,请确认是否是该任务发起人']);
            }
        } catch (\Exception $e) {
            return ['status' => 'fail', 'message' => '服务器错误'];
        }
    }

    /**
     * 删除任务
     * @param $id
     * @return int
     */
    public function deleteTask(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = FunctionTool::decrypt_id($request->id);
            $user = auth('api')->user();
            CollaborativeTask::where('initiate_id', $user->id)->where('id', $id)->delete();
            CollaborationInvitation::where('collaborative_task_id', $id)->delete();
            if ($request->notification_way != null) {
                /**
                 * 通知
                 */
                //组装动态列表单个数据格式
                $task = CollaborativeTask::find($id);
                $content = $task->title . '任务已删除'; //通知内容
                $user_ids = $task->invitations->pluck('receive_user')->toarray(); //被通知人id
                $user_ids[] = $task->principal_id;
                $this->collaborative_notify(['task' => $task, 'content' => $content, 'notification_way' => $request->notification_way, 'user_ids' => $user_ids]);
            }
            DB::commit();
            return ['status' => 'success', 'message' => '删除成功'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'fail', 'message' => '服务器错误'];
        }
    }

    /**
     * 删除附件(只删除附件关系表中的关系)
     */
    public function deleteFile(Request $request)
    {
        $file_ids = FunctionTool::decrypt_id_array(json_decode($request->deletefilesId, true));
        foreach ($file_ids as $file_id) {
            $model_id = DB::table('model_has_file')->where('file_id', $file_id)->where('model_type', CollaborativeTask::class)->value('model_id');
            $data = [
                'file_id' => $file_id,
                'model_id' => $model_id,
                'model_type' => CollaborativeTask::class,
            ];
            $this->ossFileRepository->removeFileRelation($data);
        }
        return true;
    }

    /**
     * 保存表单
     */
    public function saveForm($data)
    {
        $form_template = $data['formArea'];
        $user = \auth('api')->user();
        $id = FunctionTool::decrypt_id($data['id']);
        $task = CollaborativeTask::find($id);
        //组装文件
        $array_files = [];
        if ($form_template != null && $form_template != []) {
            foreach ($form_template as $k => $v) {
                if ($v['type'] == 'ANNEX' && isset($v['value'])) { //$v['field']['value']为数组,至少包含一个文件
                    $array_files[$k] = $v['value'];
                }
            }
        }
        /**
         * 上传文件
         */
        $company = Company::find($task->company_id);
        $js = [];
        $task_id = $id;
        if ($array_files !== null && $array_files !== []) {
            $js = CompanyOssTool::uploadFormfile($array_files, [
                'oss_path' => $company->oss->root_path . 'collaboration', //文件存入的所在目录
                'model_id' => $task_id, //关联模型id
                'model_type' => CollaborativeTask::class, //关联模型类名
                'company_id' => $task->company_id, //所属公司id
                'uploader_id' => $user->id, //上传者id
            ], $form_template);
            //更新协助表单数据
            CollaborativeTask::where('id', $task_id)->update(['form_area' => json_encode($form_template)]);
        }
        CollaborativeTask::where('id', $task_id)->update(['form_area' => json_encode($data['formArea'])]);
        if (count($js) == 0) {
            return ['status' => 'success', 'message' => '保存成功'];
        } else {
            return ['status' => 'fail', 'message' => '保存失败'];
        }
    }

    /**
     * 任务列表
     * @param Request $request
     * @return mixed
     */
    private function taskList(Request $request)
    {
        $status = $request->status;
        switch ($status) {
            case 'all':
                return $this->all($request->type, $request->internalOrExternal);
                break;
            case 'processing':
                return $this->processing($request->type, $request->internalOrExternal);
                break;
            case 'pending':
                return $this->pending($request->type, $request->internalOrExternal);
                break;
            case 'pendingReview':
                return $this->pendingReview($request->type, $request->internalOrExternal);
                break;
            case 'rejected':
                return $this->rejected($request->type, $request->internalOrExternal);
                break;
            case 'completed':
                return $this->completed($request->type, $request->internalOrExternal);
                break;
            case 'revoked':
                return $this->revoked($request->type, $request->internalOrExternal);
                break;
        }
    }

    /**
     * 所有的任务
     * @return mixed
     */
    private function all($type, $internalOrExternal)
    {
        $user = auth('api')->user();
        if ($type == 'all') { //所有 进行中的任务
            $all = $this->collaborativeRepository->allTask($user->current_company_id);
            return json_encode(array_values($all->filter(function ($all) {
                $id = auth('api')->user()->id;
                if ($all->invitations) {
                    return in_array($id, $all->invitations->pluck('receive_user')->toarray()) || ($all->initiate_id == $id) || ($all->principal_id == $id);
                } else {
                    return ($all->initiate_id == $id) || ($all->principal_id == $id);
                }
            })->toarray()));
        } elseif ($type == 'initiated') { //我发起 进行中的任务
            return json_encode($this->collaborativeRepository->myInitiateTask($user->current_company_id, $user->id));
        } elseif ($type == 'responsible') { //我负责 进行中的任务
            return json_encode($this->collaborativeRepository->myPrincipalTask($user->current_company_id, $user->id));
        } else { //我参与 进行中的任务
            $g = $this->collaborativeRepository->findTask($user->current_company_id, $user->id);
            if ($internalOrExternal === 'all' || $internalOrExternal === null) {
                return json_encode($g->filter(function ($g) {
                    return $g->task;
                }));
            } else {
                return json_encode($g->filter(function ($g) {
                    if ($g->company_id == $g->task->company_id) {
                        return $g->task;
                    } else {
                        return false;
                    }
                }));
            }
        }
    }

    /**
     * 进行中的任务
     * @return mixed
     */
    private function processing($type, $internalOrExternal)
    {
        $user = auth('api')->user();
        if ($type == 'all') { //所有 进行中的任务
            $all = $this->collaborativeRepository->allTasking($user->current_company_id);
            return json_encode(array_values($all->filter(function ($all) {
                $id = auth('api')->user()->id;
                if ($all->invitations) {
                    return in_array($id, $all->invitations->pluck('receive_user')->toarray()) || ($all->initiate_id == $id) || ($all->principal_id == $id);
                } else {
                    return ($all->initiate_id == $id) || ($all->principal_id == $id);
                }
            })->toarray()));
        } elseif ($type == 'initiated') { //我发起 进行中的任务
            return json_encode($this->collaborativeRepository->myInitiateTasking($user->current_company_id, $user->id));
        } elseif ($type == 'responsible') { //我负责 进行中的任务
            return json_encode($this->collaborativeRepository->myPrincipalTasking($user->current_company_id, $user->id));
        } else { //我参与 进行中的任务
            $g = $this->collaborativeRepository->findTasking($user->current_company_id, $user->id);
            if ($internalOrExternal === 'all' || $internalOrExternal === null) {
                return json_encode($g->filter(function ($g) {
                    return $g->task;
                }));
            } else {
                return json_encode($g->filter(function ($g) {
                    if ($g->company_id == $g->task->company_id) {
                        return $g->task;
                    } else {
                        return false;
                    }
                }));
            }
        }
    }

    /**
     * 待接收的任务
     */
    private function pending($type, $internalOrExternal)
    {
        $user = auth('api')->user();
        if ($type == 'all') { //所有 待接收的任务
            $all = $this->collaborativeRepository->allPendingReceptionTasks($user->current_company_id);
            return json_encode(array_values($all->filter(function ($all) {
                $user_id = auth('api')->user()->id;
                if ($all->invitations) {
                    return in_array($user_id, $all->invitations->pluck('receive_user')->toarray()) || ($all->initiate_id == $user_id) || ($all->principal_id == $user_id);
                } else {
                    return ($all->initiate_id == $user_id) || ($all->principal_id == $user_id);
                }
            })->toarray()));
        } elseif ($type == 'initiated') { //我发起 待接收的任务
            return json_encode($this->collaborativeRepository->myInitiatePendingReceptionTasks($user->current_company_id, $user->id));
        } elseif ($type == 'responsible') { //我负责 待接收的任务
            return json_encode($this->collaborativeRepository->myPrincipalPendingReceptionTasks($user->current_company_id, $user->id));
        } else { //我参与 待接收的任务
            $g = $this->collaborativeRepository->findUnprocessedTask($user->current_company_id, $user->id);
            if ($internalOrExternal === 'all' || $internalOrExternal === null) {
                return json_encode($g->filter(function ($g) {
                    return $g->task;
                }));
            } else {
                return json_encode($g->filter(function ($g) {
                    if ($g->company_id == $g->task->company_id) {
                        return $g->task;
                    } else {
                        return false;
                    }
                }));
            }
        }
    }

    /**
     * 待审核的任务
     */
    private function pendingReview($type, $internalOrExternal)
    {
        $user = auth('api')->user();
        if ($type == 'all') { //所有 待审核的任务
            $all = $this->collaborativeRepository->allPendingReviewTasks($user->current_company_id);
            return json_encode(array_values($all->filter(function ($all) {
                $user_id = auth('api')->user()->id;
                return ($all->initiate_id == $user_id) || ($all->principal_id == $user_id);
            })->toarray()));
        } elseif ($type == 'initiated') { //我发起 待审核的任务
            return json_encode($this->collaborativeRepository->myInitiatePendingReviewTasks($user->current_company_id, $user->id));
        } elseif ($type == 'responsible') { //我负责 待审核的任务
            return json_encode($this->collaborativeRepository->myPrincipalPendingReviewTasks($user->current_company_id, $user->id));
        } else { //我参与 待审核的任务
            return json_encode([]);
        }
    }

    /**
     * 已拒绝的任务
     */
    private function rejected($type, $internalOrExternal)
    {
        $user = auth('api')->user();
        if ($type == 'all') { //所有 已拒绝的任务
            $all = $this->collaborativeRepository->allRejectedTask($user->current_company_id);
            return json_encode(array_values($all->filter(function ($all) {
                $user_id = auth('api')->user()->id;
                if ($all->invitations) {
                    return in_array($user_id, $all->invitations->pluck('receive_user')->toarray()) || ($all->initiate_id == $user_id) || ($all->principal_id == $user_id);
                } else {
                    return ($all->initiate_id == $user_id) || ($all->principal_id == $user_id);
                }
            })->toarray()));
        } elseif ($type == 'initiated') { //我发起 已拒绝的任务
            return json_encode($this->collaborativeRepository->myInitiateRejectedTask($user->current_company_id, $user->id));
        } elseif ($type == 'responsible') { //我负责 已拒绝的任务
            return json_encode($this->collaborativeRepository->myPrincipalRejectedTask($user->current_company_id, $user->id));
        } else { //我参与 已拒绝的任务
            $g = $this->collaborativeRepository->findRefuseTasked($user->current_company_id, $user->id);
            if ($internalOrExternal === 'all' || $internalOrExternal === null) {
                return json_encode($g->filter(function ($g) {
                    return $g->task;
                }));
            } else {
                return json_encode($g->filter(function ($g) {
                    if ($g->company_id == $g->task->company_id) {
                        return $g->task;
                    } else {
                        return false;
                    }
                }));
            }
        }
    }

    /**
     * 已完成的任务
     */
    private function completed($type, $internalOrExternal)
    {
        $user = auth('api')->user();
        if ($type == 'all') { //所有 已完成的任务
            $all = $this->collaborativeRepository->allCompletedTask($user->current_company_id);
            return json_encode(array_values($all->filter(function ($all) {
                $user_id = auth('api')->user()->id;
                if ($all->invitations) {
                    return in_array($user_id, $all->invitations->pluck('receive_user')->toarray()) || ($all->initiate_id == $user_id) || ($all->principal_id == $user_id);
                } else {
                    return ($all->initiate_id == $user_id) || ($all->principal_id == $user_id);
                }
            })->toarray()));
        } elseif ($type == 'initiated') { //我发起 已完成的任务
            return json_encode($this->collaborativeRepository->myInitiatecompletedTask($user->current_company_id, $user->id));
        } elseif ($type == 'responsible') { //我负责 已完成的任务
            return json_encode($this->collaborativeRepository->myPrincipalcompletedTask($user->current_company_id, $user->id));
        } else { //我参与 已完成的任务
            $g = $this->collaborativeRepository->findTasked($user->current_company_id, $user->id);
            if ($internalOrExternal === 'all' || $internalOrExternal === null) {
                return json_encode($g->filter(function ($g) {
                    return $g->task;
                }));
            } else {
                return json_encode($g->filter(function ($g) {
                    if ($g->company_id == $g->task->company_id) {
                        return $g->task;
                    } else {
                        return false;
                    }
                }));
            }
        }
    }

    /**
     * 已撤销的
     */
    private function revoked($type, $internalOrExternal)
    {
        $user = auth('api')->user();
        if ($type == 'all') { //所有 已撤销的任务
            $all = $this->collaborativeRepository->allRevokedTask($user->current_company_id);
            return json_encode(array_values($all->filter(function ($all) {
                $id = auth('api')->user()->id;
                if ($all->invitations) {
                    return in_array($id, $all->invitations->pluck('receive_user')->toarray()) || ($all->initiate_id == $id) || ($all->principal_id == $id);
                } else {
                    return ($all->initiate_id == $id) || ($all->principal_id == $id);
                }
            })->toarray()));
        } elseif ($type == 'initiated') { //我发起 进行中的任务
            return json_encode($this->collaborativeRepository->myInitiateRevokedTask($user->current_company_id, $user->id));
        } elseif ($type == 'responsible') { //我负责 进行中的任务
            return json_encode($this->collaborativeRepository->myPrincipalRevokedTask($user->current_company_id, $user->id));
        } else { //我参与 进行中的任务
            $g = $this->collaborativeRepository->findTask($user->current_company_id, $user->id);
            if ($internalOrExternal === 'all' || $internalOrExternal === null) {
                return json_encode($g->filter(function ($g) {
                    if ($g->task->is_delete == 1) {
                        return $g->task;
                    } else {
                        return false;
                    }
                }));
            } else {
                return json_encode($g->filter(function ($g) {
                    if ($g->company_id == $g->task->company_id && $g->task->is_delete == 1) {
                        return $g->task;
                    } else {
                        return false;
                    }
                }));
            }
        }
    }

    /**
     * 合作伙伴协助
     */
    public function partner_assistance($request)
    {
        $data = [];
        $taskData = json_decode($this->deal_partner_assistance($request->status), true);
        $all_count = count($taskData);
        $taskList = array_slice($taskData, $request->offset, $request->limit);
        foreach ($taskList as $value) {
            $status = $this->left_s($value['task']);
            $data[] = [
                'id' => FunctionTool::encrypt_id($value['task']['id']),
                'title' => $value['task']['title'],
                'initiate' => ['initiate_id' => $value['task']['initiate_id'], 'initiate_name' => User::find($value['task']['initiate_id'])->name],
                'description' => $value['task']['description'],
                'created_at' => $value['task']['created_at'],
                'limit_time' => $value['task']['limit_time'],
                'status' => $status,
                'is_cancel' => $value['task']['is_delete'],
            ];
        }
        $data = ['all_count' => $all_count, 'data' => $data];
        return json_encode($data);
    }
    private function left_s($task)
    {
        if ($task['status'] == 1) {
            return '已完成';
        } else {
            if ($task['is_receive'] == 0) {
                return '待接收';
            } elseif ($task['is_receive'] == 1) {
                return '进行中';
            } elseif ($task['is_receive'] == 2) {
                return '已拒绝';
            } elseif ($task['is_receive'] == 3) {
                return '待审核';
            } else {
                return '';
            }
        }
    }
    private function deal_partner_assistance($status)
    {
        $user = \auth('api')->user();
        if ($status == 'all') {
            $g = $this->collaborativeRepository->findTask($user->current_company_id, $user->id);
            return json_encode($g->filter(function ($g) {
                if ($g->company_id != $g->task->company_id) {
                    return $g->task;
                } else {
                    return false;
                }
            }));
        } elseif ($status == 'processing') { //进行中
            $g = $this->collaborativeRepository->findTasking($user->current_company_id, $user->id);
            return json_encode($g->filter(function ($g) {
                if ($g->company_id != $g->task->company_id) {
                    return $g->task;
                } else {
                    return false;
                }
            }));
        } elseif ($status == 'pending') { //待接收
            $g = $this->collaborativeRepository->findUnprocessedTask($user->current_company_id, $user->id);
            return json_encode($g->filter(function ($g) {
                if ($g->company_id != $g->task->company_id) {
                    return $g->task;
                } else {
                    return false;
                }
            }));
        } elseif ($status == 'pendingReview') { //待审核
            return json_encode([]);
        } elseif ($status == 'rejected') { //已拒绝
            $g = $this->collaborativeRepository->findRefuseTasked($user->current_company_id, $user->id);
            return json_encode($g->filter(function ($g) {
                if ($g->company_id != $g->task->company_id) {
                    return $g->task;
                } else {
                    return false;
                }
            }));
        } elseif ($status == 'completed') { //已完成
            $g = $this->collaborativeRepository->findTasked($user->current_company_id, $user->id);
            return json_encode($g->filter(function ($g) {
                if ($g->company_id != $g->task->company_id) {
                    return $g->task;
                } else {
                    return false;
                }
            }));
        } elseif ($status == 'revoked') { //已撤销
            $g = $this->collaborativeRepository->findTask($user->current_company_id, $user->id);
            return json_encode($g->filter(function ($g) {
                if ($g->company_id != $g->task->company_id && $g->task->is_delete == 1) {
                    return $g->task;
                } else {
                    return false;
                }
            }));
        }
    }
    /**
     * 搜索查询
     * @param $type
     * @param $title
     */
    private function s_search($status, $title)
    {
        $user = auth('api')->user();
        if ($status == 'all') {
            $all = $this->collaborativeRepository->searchAllTask($user->current_company_id, $title, $user->id);
            return json_encode($all->filter(function ($all) {
                $user_id = auth('api')->user()->id;
                if ($all->invitations) {
                    return in_array($user_id, $all->invitations->pluck('receive_user')->toarray()) || ($all->initiate_id == $user_id) || ($all->principal_id == $user_id);
                } else {
                    return ($all->initiate_id == $user_id) || ($all->principal_id == $user_id);
                }
            }));
        } elseif ($status == 'processing') {
            $all = $this->collaborativeRepository->allSearchTasking($user->current_company_id, $title, $user->id);
            return json_encode($all->filter(function ($all) {
                $user_id = auth('api')->user()->id;
                if ($all->invitations) {
                    return in_array($user_id, $all->invitations->pluck('receive_user')->toarray()) || ($all->initiate_id == $user_id) || ($all->principal_id == $user_id);
                } else {
                    return ($all->initiate_id == $user_id) || ($all->principal_id == $user_id);
                }
            }));
        } elseif ($status == 'pending') {
            $all = $this->collaborativeRepository->allSearchPendingReceptionTasks($user->current_company_id, $title, $user->id);
            return json_encode($all->filter(function ($all) {
                $user_id = auth('api')->user()->id;
                if ($all->invitations) {
                    return in_array($user_id, $all->invitations->pluck('receive_user')->toarray()) || ($all->initiate_id == $user_id) || ($all->principal_id == $user_id);
                } else {
                    return ($all->initiate_id == $user_id) || ($all->principal_id == $user_id);
                }
            }));
        } elseif ($status == 'pendingReview') {
            $all = $this->collaborativeRepository->allSearchPendingReviewTasks($user->current_company_id, $title, $user->id);
            return json_encode($all->filter(function ($all) {
                $user_id = auth('api')->user()->id;
                return ($all->initiate_id == $user_id) || ($all->principal_id == $user_id);
            }));
        } elseif ($status == 'rejected') {
            $all = $this->collaborativeRepository->allSearchRejectedTask($user->current_company_id, $title, $user->id);
            return json_encode($all->filter(function ($all) {
                $user_id = auth('api')->user()->id;
                if ($all->invitations) {
                    return in_array($user_id, $all->invitations->pluck('receive_user')->toarray()) || ($all->initiate_id == $user_id) || ($all->principal_id == $user_id);
                } else {
                    return ($all->initiate_id == $user_id) || ($all->principal_id == $user_id);
                }
            }));
        } elseif ($status == 'completed') {
            $all = $this->collaborativeRepository->allSearchCompletedTask($user->current_company_id, $title, $user->id);
            return json_encode($all->filter(function ($all) {
                $user_id = auth('api')->user()->id;
                if ($all->invitations) {
                    return in_array($user_id, $all->invitations->pluck('receive_user')->toarray()) || ($all->initiate_id == $user_id) || ($all->principal_id == $user_id);
                } else {
                    return ($all->initiate_id == $user_id) || ($all->principal_id == $user_id);
                }
            }));
        } elseif ($status == 'revoked') {
            $all = $this->collaborativeRepository->allSearchevokedTask($user->current_company_id, $title, $user->id);
            return json_encode($all->filter(function ($all) {
                $user_id = auth('api')->user()->id;
                if ($all->invitations) {
                    return in_array($user_id, $all->invitations->pluck('receive_user')->toarray()) || ($all->initiate_id == $user_id) || ($all->principal_id == $user_id);
                } else {
                    return ($all->initiate_id == $user_id) || ($all->principal_id == $user_id);
                }
            }));
        }
    }

    /**
     * @param $data
     * @throws \ReflectionException
     * 协助通知
     */
    private function collaborative_notify($data)
    {
        $user = auth('api')->user();
        $company = Company::find($user->current_company_id);
        $single_data = DynamicTool::getSingleListData(
            CollaborativeTask::class,
            1,
            'company_id',
            $company->id,
            $company->name,
            array_get($data, 'content'),
            date('Y-m_d H:i:s', time())
        );
        return NotifyTool::publishNotify(array_get($data, 'user_ids'), $user->current_company_id, array_get($data, 'task'), array_get($data, 'notification_way'), $single_data, []); //此处方法顺序待调整
    }

    /**
     * 测试
     */
    public function testCollaborative(Request $request)
    {
        //        return $this->deleteTask($request);
        //        $a = $this->deleteFile($request);
        //        dd($a);
        $this->cancel($request);
        //        $client = new Client();//Client()不传参数时,会把 127.0.0.1 和 6379 作为默认的host 和 port 并且连接超时时间是 5 秒
        //        $client->set('queue','default');
        //        $a=$client->get('queue');
        //        dd($a);
        //连接本地的 Redis 服务
        //        $redis = Redis::class;
        //        dd($redis);
        //        echo "Connection to server sucessfully";
        //        echo "Server is running: " . $redis->get('12');//查看redis是否开启
        //        exit();

        //        $a = $this->collaborativeRepository->principalDeleteTask(14);
        //        $a = $this->collaborativeRepository->leadingTast(3);
        //        dd($a);
        //        $a = $this->collaborativeRepository->findTask();

        //        $a = CollaborationInvitation::find(8)->task->invitations;
        //        $b = CollaborativeTask::find(2);
        return $this->sendInvite();
    }
}
