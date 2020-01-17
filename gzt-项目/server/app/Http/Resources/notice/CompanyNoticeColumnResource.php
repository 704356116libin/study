<?php

namespace App\Http\Resources\notice;

use App\Tools\FunctionTool;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyNoticeColumnResource extends JsonResource
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
            'company_id'=>FunctionTool::encrypt_id($this->company_id),
            'name'=>$this->name,
            'description'=>$this->description,
            'order'=>$this->order,
        ];
    }
}
