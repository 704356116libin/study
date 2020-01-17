<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Tools\DepartmentTool;
use App\Tools\FunctionTool;
use Illuminate\Http\Resources\Json\JsonResource;
use \App\Http\Resources\CompanyUserResource;

class DepartmentResource extends JsonResource
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
            'id'=>FunctionTool::getFunctionTool()->encrypt_id($this->id),//节点id
            'name'=>$this->name,                                         //节点名称
            'parent_id'=>FunctionTool::getFunctionTool()->encrypt_id($this->parent_id),//节点父id
            'number_people'=>DepartmentTool::getStaffNum($this->id),        //节点下的员工数(包含所有子节点)
            'depth'=>$this->depth,//节点的深度
            'users'=>CompanyUserResource::collection($this->users),                //该节点下的员工信息
            'children'=>DepartmentResource::collection($this->children),    //子节点信息
        ];
    }
}
