<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\CollaborationInvitationEvent;
use App\Tools\CollaborativeTool;
use Illuminate\Http\Request;

class CollaborationController extends Controller
{
    private $collaborativeTool;

    /**
     * 构造函数
     * CollaborationController constructor.
     */
    public function __construct(CollaborativeTool $collaborativeTool)
    {
        $this->collaborativeTool = $collaborativeTool::getCollaborativeTool();
    }

    /**
     * 邀请者发起协作任务并邀请好友或公司人员
     */
    public function sendInvite(Request $request)
    {
        return $this->collaborativeTool->sendInvite($request->all());
    }
    /**
     * 合作伙伴协助
     */
    public function partner_assistance(Request $request)
    {
        return $this->collaborativeTool->partner_assistance($request);
    }

    /**
     * 编辑任务
     */
    public function editTask(Request $request)
    {
        return $this->collaborativeTool->editTask($request);
    }

    /**
     * 附件预览或下载
     */
    public function fileViewOrupload()
    {
        return $this->collaborativeTool->fileViewOrupload();
    }

    /**
     * 协助列表
     */
    public function taskList(Request $request)
    {
        return $this->collaborativeTool->dealTaskList($request);
    }

    /**
     * 搜索查询
     */
    public function search(Request $request)
    {
        return $this->collaborativeTool->search($request);
    }

    /**
     * @param $task_id
     * @return \App\Http\Resources\TaskResource
     * 任务详情
     */
    public function taskDetail(Request $request)
    {
        return $this->collaborativeTool->taskDetail($request->id);
    }

    /**
     * 点击接受按钮
     * @param Request $request
     */
    public function receiveButton(Request $request)
    {
        return $this->collaborativeTool->receiveButton($request);
    }

    /**
     * 点击转交按钮
     */
    public function transferButton(Request $request)
    {
        return $this->collaborativeTool->transferButton($request);
    }

    /**
     * 转交列表
     */
    public function transferList()
    {
        return $this->collaborativeTool->transferList();
    }

    /**
     * 操作(接收转交任务,拒绝转交任务)
     */
    public function transferOperating(Request $request)
    {
        return $this->collaborativeTool->transferOperating($request);
    }

    /**
     * 点击拒绝
     */
    public function rejectButton(Request $request)
    {
        return $this->collaborativeTool->rejectButton($request);
    }

    /**
     * 点击完成
     */
    public function carryOutButton(Request $request)
    {
        return $this->collaborativeTool->carryOutButton($request);
    }

    /**
     * 发起人审核
     */
    public function auditButton(Request $request)
    {
        return $this->collaborativeTool->auditButton($request);
    }

    /**
     * 发起人撤销任务
     */
    public function cancel(Request $request)
    {
        return $this->collaborativeTool->cancel($request);
    }

    /**
     * 发起者恢复任务
     * @param Request $request
     * @return mixed
     */
    public function recoveryTask(Request $request)
    {
        return $this->collaborativeTool->recoveryTask($request);
    }

    /**
     * 删除任务
     */
    public function deleteTask(Request $request)
    {
        return $this->collaborativeTool->deleteTask($request);
    }

    /**
     * 删除附件
     */
    public function deleteFile(Request $request)
    {
        return $this->collaborativeTool->deleteFile($request);
    }

    /**
     * 保存表单
     */
    public function saveForm(Request $request)
    {
        return $this->collaborativeTool->saveForm($request->all());
    }

    /**
     * 测试
     */
    public function testCollaborative(Request $request)
    {
        return $this->collaborativeTool->testCollaborative($request);
    }
}
