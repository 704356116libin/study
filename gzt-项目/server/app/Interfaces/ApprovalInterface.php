<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface ApprovalInterface
{
    public function addApprovalType($data);//添加审批类型

    public function addApprovalTemplate($data);//添加流程模板

    public function createApproval(array $request);//创建审批

    public function ApprovalList($data);//审批列表

    public function detail($id);//审批详情

    public function deleteApprovalType($id);//删除类型

    public function ablTemplateList($type_id);//查找可用的模板

    public function selectTem($id);//选择一个模板

    public function sysTemList();//管理模板-模板列表

    public function saveSequence($array);//审批类型拖拽排序保存入库

    public function editApprovalType($array);//管理模板--编辑审批类型

    public function editTemplate($id);//编辑模板

    public function deleteTemplate($id);// 删除审批模板

    public function saveEditTemplate($data);//提交模板编辑

    public function againApply(Request $request);//再次申请按钮

    public function agree($approval_id, $opinion, $notification_way);//审批操作(同意)

    public function refuse($approval_id, $opinion, $notification_way);//审批操作(拒绝)

    public function transfer($data);//转交

    public function cancel($approval_id, $opinion);//审批操作(撤销)

    public function archive($approval_id, $opinion);//归档

    public function urgent(Request $request);//催办

    public function approvalClassicTemplate();//审批经典模板

    public function existingTemplate();//已有模板

    public function approvalTypeList();//审批类型列表

    public function deleteFile(Request $request);//删除附件(只删除附件关系表中的关系)

}
