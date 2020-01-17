<?php

namespace App\Http\Resources\department;

use App\Tools\DepartmentTool;
use App\Tools\FunctionTool;
use Illuminate\Http\Resources\Json\JsonResource;

class ExternalContactResource extends JsonResource
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
            'external_contact'=>DepartmentTool::allExternalContact($this->id),
        ];
    }
}
