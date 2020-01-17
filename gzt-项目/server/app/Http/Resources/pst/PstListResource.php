<?php

namespace App\Http\Resources\pst;

use App\Models\User;
use App\Tools\FunctionTool;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
/**
 * 评审通列表展示资源文件
 * Class PstTemplateResource
 * @package App\Http\Resources\pst
 */
class PstListResource extends JsonResource
{
    public static $is_inside=false;//标识是否为内部参与人
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
            'form_values'=>is_null(json_decode($this->form_values))?:json_decode($this->form_values),//项目名称
            'process_template'=>json_decode($this->process_template),//评审人员流程信息
            'approval_method'=>$this->approval_method,//流程类型
            'removed'=>$this->removed,//是否处于软删除状态
            'form_template'=>json_decode($this->form_template),//表单数据
            'join_form_data'=>json_decode($this->join_user_data),//参与人员表单选择数据
            'cc_form_data'=>json_decode($this->cc_user_data),//抄送人员表单选择数据
            'duty_user_data'=>json_decode($this->duty_user_data),
            'workingDays' => Carbon::parse($this->created_at)->diffInWeekdays(),
            'created_at'=>date(config('basic.date_format'),strtotime($this->created_at)),//创建时间
            'updated_at'=>date(config('basic.date_format'),strtotime($this->updated_at)),//更新时间
        ];
    }
}
