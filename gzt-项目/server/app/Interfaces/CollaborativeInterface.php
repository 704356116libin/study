<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface CollaborativeInterface
{
    public function sendInvite($request);   //发起协作

    public function editTask(Request $request);     //编辑任务

    public function dealTaskList(Request $request); //任务列表

    public function search(Request $request);       //搜索列表

    public function taskDetail($task_id);           //任务详情

    public function receiveButton(Request $request);//点击接收

    public function rejectButton(Request $request); //点击拒绝

    public function carryOutButton(Request $request);  //点击完成

    public function auditButton(Request $request);  //发起人审核(是否任务通过)

    public function cancel(Request $request);       //发起人撤销任务

    public function recoveryTask(Request $request); //发起人恢复任务

    public function testCollaborative(Request $request);//测试

    public function deleteTask(Request $request);//删除任务

    public function deleteFile(Request $request);//删除附件(只删除附件关系表中的关系)

    public function saveForm($data);//保存表单
}
