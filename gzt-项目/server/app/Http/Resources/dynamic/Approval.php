<?php

namespace App\Http\Resources\dynamic;

use App\Tools\ApprovalTool;
use App\Tools\CompanyNoticeTool;
use App\Tools\FunctionTool;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 企业公告--通知的json
 */
class Approval extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=>FunctionTool::encrypt_id($this->id),
            'type'=>$this->approvalType->name,
            'approval_content'=>ApprovalTool::approvalContent(json_decode($this->form_template)),
            'created_at'=>Carbon::parse($this->created_at)->toDateTimeString(),
            'applicant'=>$this->user->name,
            'complete_time'=>$this->complete_time,
            'status'=>$this->cancel_or_archive==1?'已撤销':($this->cancel_or_archive==2?'已归档':($this->status==0?'审批中':'已完成')),
        ];
    }
}
