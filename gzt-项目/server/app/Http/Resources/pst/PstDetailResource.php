<?php

namespace App\Http\Resources\pst;

use App\Models\User;
use App\Tools\FunctionTool;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 评审通详情资源文件
 * Class PstTemplateResource
 * @package App\Http\Resources\pst
 */
class PstDetailResource extends JsonResource
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
            'state'=>$this->state,//评审通总的状态
            'approval_method'=>$this->approval_method,//评审通审批类型
            'company_id'=>FunctionTool::encrypt_id($this->company_id),
            'publish_user'=>is_null(User::find($this->publish_user_id))?null:User::find($this->publish_user_id)->name,
            'need_approval'=>$this->need_approval,
            'process_template'=>json_decode($this->process_template),//评审人员流程信息
            'approval_method'=>$this->approval_method,//流程类型
            'removed'=>$this->removed,//是否处于软删除状态
            'form_template'=>json_decode($this->form_template),//表单数据
            'join_form_data'=>[],//参与人员表单选择数据
            'cc_form_data'=>[],//抄送人员表单选择数据
            'duty_user_data'=>[],
            'created_at'=>date(config('basic.date_format'),strtotime($this->created_at)),//创建时间
            'updated_at'=>date(config('basic.date_format'),strtotime($this->updated_at)),//更新时间
            'relative_pst'=>[],//关联评审的信息组
            'relative_approval'=>[],//关联的审批信息组
        ];
    }
}
