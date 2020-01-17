<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Repositories\ApprovalTemplateRepository;
use App\Tools\ApprovalTool;
use App\Tools\FunctionTool;
use Illuminate\Http\Resources\Json\JsonResource;
class ApprovalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $content=[];
        if(json_decode($this->form_template)!=null&&json_decode($this->form_template)!=[]){
            foreach (json_decode($this->form_template) as $v){
                if($v->type=='ANNEX'){
                    $v->value=FileResource::collection($this->files);
                };
                $content[]=$v;
            }
        }
        return [
            'id'=>FunctionTool::encrypt_id($this->id),//任务id
            'name'=>$this->name,//模板名
            'content'=>$content,
            'approval_number'=>$this->numbering,//审批编号
            'approval_method'=>$this->approval_method,//流程方式
            'end_status'=>$this->cancel_or_archive==1?'已撤销':($this->cancel_or_archive==2?'已归档':($this->end_status==0?'审批中':($this->end_status==1?'同意':'拒绝'))),
            'form_template'=>json_decode($this->form_template),//表单数据
            'process_template'=>ApprovalTemplateRepository::processData($this->approvalUsers->groupBy('approval_level')),
            'sponsor_data'=>ApprovalTemplateRepository::sponsorData($this),
            'button_status'=>ApprovalTemplateRepository::myIdentity($this->id),
            'cc_my'=>User::whereIn('id',$this->cc->pluck('user_id')->toarray())->get()
                ->map(function ($user){
                    return ['id'=>$user->id,'name'=>$user->name];
                }),
        ];
    }
}
