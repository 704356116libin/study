<?php

namespace App\Http\Resources\department;

use App\Http\Resources\CompanyUserResource;
use App\Http\Resources\user\UserSimpleResource;
use App\Models\User;
use App\Tools\BusinessManagementTool;
use App\Tools\DepartmentTool;
use App\Tools\FunctionTool;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentSimpleResource extends JsonResource
{
    public static $company_id;
    public static $activation=1;
//    public static $users=false;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        UserSimpleResource::$company_id=self::$company_id;
        SingleNodeResource::$company_id=self::$company_id;
        return [
            'id'=>FunctionTool::getFunctionTool()->encrypt_id($this->id),//节点id
            'name'=>$this->name,                                         //节点名称
            'number_people'=>DepartmentTool::getStaffNum($this->id),     //节点下的员工数(包含所有子节点)
//            'users'=>UserSimpleResource::collection($this->users),                //该节点下的员工信息
            'users'=>CompanyUserResource::collection(BusinessManagementTool::companyUesr($this->users,self::$activation,self::$company_id)),
            'children'=>DepartmentSimpleResource::collection($this->children),
        ];
    }
}
