<?php

namespace App\Http\Controllers\Api;

use App\Exports\ApprovalExport;
use App\Tools\ApprovalTool;
use Illuminate\Http\Request;
use App\Exports\ApprovalExportPdf;

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/12/25
 * Time: 8:58
 */
class ApprovalController extends Controller
{
    private $getApprovalTool;

    /**
     * ApprovalController constructor.
     */
    public function __construct(ApprovalTool $approvalTool)
    {
        $this->getApprovalTool = $approvalTool::getApprovalTool();
    }

    /**
     * 添加审批类型
     */
    public function addApprovalType(Request $request)
    {
        return $this->getApprovalTool->addApprovalType($request->all());
    }

    /**
     * 删除类型
     */
    public function deleteApprovalType(Request $request)
    {
        return $this->getApprovalTool->deleteApprovalType($request->id);
    }

    /**
     * 添加审批模板
     */
    public function addApprovalTemplate(Request $request)
    {
        return $this->getApprovalTool->addApprovalTemplate($request->all());
    }

    /**
     * 管理模板--列表
     */
    public function sysTemList()
    {
        return $this->getApprovalTool->sysTemList();
    }

    /**
     * 管理模板--审批类型拖拽排序保存到数据库
     */
    public function saveSequence(Request $request)
    {
        return $this->getApprovalTool->saveSequence($request->all());
    }

    /**
     * 管理模板--编辑审批类型
     */
    public function editApprovalType(Request $request)
    {
        return $this->getApprovalTool->editApprovalType($request->all());
    }

    /**
     * 审批管理--编辑审批模板
     */
    public function editTemplate(Request $request)
    {
        return $this->getApprovalTool->editTemplate($request->id);
    }

    /**
     * 删除审批模板
     */
    public function deleteTemplate(Request $request)
    {
        return $this->getApprovalTool->deleteTemplate($request->id);
    }

    /**
     * 是否启用模板
     */
    public function isShow(Request $request)
    {
        return $this->getApprovalTool->isShow($request->all());
    }

    /**
     * 管理模板保存编辑后的审批模板
     */
    public function saveEditTemplate(Request $request)
    {
        return $this->getApprovalTool->saveEditTemplate($request->all());
    }

    /**
     * 查找所有可用的模板
     */
    public function ablTemplateList(Request $request)
    {
        return $this->getApprovalTool->ablTemplateList($request->type_id);
    }

    /**
     * @param Request $request
     * @return mixed
     * 搜索框搜索可用模板(通过name)
     */
    public function searchAblTemplate(Request $request)
    {
        return $this->getApprovalTool->searchAblTemplate($request->name);
    }

    /**
     * 选择模板
     */
    public function selectTem(Request $request)
    {
        return $this->getApprovalTool->selectTem($request->id);
    }

    /**
     * 创建审批
     */
    public function createApproval(Request $request)
    {
        return $this->getApprovalTool->createApproval($request->all());
    }

    /**
     * 再次申请按钮
     */
    public function againApply(Request $request)
    {
        return $this->getApprovalTool->againApply($request);
    }

    /**
     * 审批列表
     */
    public function ApprovalList(Request $request)
    {
        return $this->getApprovalTool->ApprovalList($request->all());
    }

    /**
     * 审批详情
     */
    public function detail(Request $request)
    {
        return $this->getApprovalTool->detail($request->id);
    }

    /**
     * 导出审批数据
     */
    public function export(Request $request, ApprovalExport $approvalExport, ApprovalExportPdf $approvalExportPdf)
    {
        switch ($request->export_type){
            case 'excel':
                return $approvalExport->whereLimit($request->data);
                break;
            case 'pdf':
                return $approvalExportPdf->ApprovalExportPdf($request->data);
                break;
        }
    }

    /**
     * 经典审批模板列表
     */
    public function approvalClassicTemplate()
    {
        return $this->getApprovalTool->approvalClassicTemplate();
    }

    /**
     * 已有模板
     */
    public function existingTemplate()
    {
        return $this->getApprovalTool->existingTemplate();
    }

    /**
     * 审批类型列表
     */
    public function approvalTypeList()
    {
        return $this->getApprovalTool->approvalTypeList();
    }

    /**
     * 同意
     * @param Request $request
     * @return mixed
     */
    public function agree(Request $request)
    {
        return $this->getApprovalTool->agree($request->approval_id, $request->opinion, $request->notification_way);
    }

    /**
     * 拒绝
     * @param Request $request
     * @return mixed
     */
    public function refuse(Request $request)
    {
        return $this->getApprovalTool->refuse($request->approval_id, $request->opinion, $request->notification_way);
    }

    /**
     * 转交
     * @param Request $request
     * @return mixed
     */
    public function transfer(Request $request)
    {
        return $this->getApprovalTool->transfer($request->all());
    }

    /**
     * 撤销
     * @param Request $request
     * @return mixed
     */
    public function cancel(Request $request)
    {
        return $this->getApprovalTool->cancel($request->approval_id, $request->opinion);
    }

    /**
     * 归档
     * @param Request $request
     * @return mixed
     */
    public function archive(Request $request)
    {
        return $this->getApprovalTool->archive($request->approval_id, $request->opinion);
    }

    /**
     * 催办
     */
    public function urgent(Request $request)
    {
        return $this->getApprovalTool->urgent($request);
    }

    /**
     * 删除附件
     */
    public function deleteFile(Request $request)
    {
        return $this->collaborativeTool->deleteFile($request);
    }
}