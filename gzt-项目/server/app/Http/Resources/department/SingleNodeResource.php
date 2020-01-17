<?php

namespace App\Http\Resources\department;

use App\Http\Resources\user\UserSimpleResource;
use App\Models\User;
use App\Tools\DepartmentTool;
use App\Tools\FunctionTool;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleNodeResource extends JsonResource
{
    public static $company_id;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        UserSimpleResource::$company_id=self::$company_id;
        return [
            'id'=>FunctionTool::getFunctionTool()->encrypt_id($this->id),//节点id
            'name'=>$this->name,                                         //节点名称
            'number_people'=>DepartmentTool::getStaffNum($this->id),     //节点下的员工数(包含所有子节点)
        ];
    }
}
