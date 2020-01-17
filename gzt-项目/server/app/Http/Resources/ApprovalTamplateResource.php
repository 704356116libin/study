<?php

namespace App\Http\Resources;

use App\Repositories\ApprovalTemplateRepository;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
class ApprovalTamplateResource extends JsonResource
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
            'count'=>$this->approvals->count(),//该类型下审批数量
            'typeId'=>$this->id,
            'type'=>$this->name,
            'data'=>$this->templates->filter(function ($value){
                return $value->is_show==1&&$value->company_id==auth('api')->user()->current_company_id;
            })->map(function ($templates){
                return ['id'=>$templates->id,'name'=>$templates->name,'is_show'=>$templates->is_show,'description'=>$templates->description,
                    'approval_method'=>$templates->approval_method,'updated_time'=>Carbon::parse($templates->updated_at)->toDateTimeString(),
                    'per'=>ApprovalTemplateRepository::ableRange($templates->per)];//获取拥有该模板使用权限的部门或用户
            })->toArray(),//模板
        ];
    }
}
