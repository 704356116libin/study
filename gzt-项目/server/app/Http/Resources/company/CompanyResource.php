<?php

namespace App\Http\Resources\company;

use App\Models\User;
use App\Tools\FunctionTool;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 企业or组织信息 json
 * Class CompanyNoticeResource
 * @package App\Http\Resources
 */
class CompanyResource extends JsonResource
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
            'name'=>$this->name,
            'provice'=>$this->province,
            'city'=>$this->city,
            'creator_name'=>User::find($this->creator_id)->name,
            'created_at'=>$this->created_at,
        ];
    }
}
