<?php

namespace App\Http\Resources\department;

use App\Http\Resources\company\CompanyUserInfoResource;
use App\Http\Resources\user\UserSimpleResource;
use App\Models\User;
use App\Tools\DepartmentTool;
use App\Tools\FunctionTool;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentSingleResource extends JsonResource
{
    public static $company_id;
    public static $users;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        CompanyUserInfoResource::$company_id=self::$company_id;
        UserSimpleResource::$company_id=self::$company_id;
        SingleNodeResource::$company_id=self::$company_id;
        return [
            'users'=>CompanyUserInfoResource::collection(self::$users),                //该节点下的员工信息
            'children'=>SingleNodeResource::collection($this->children),    //子节点信息
        ];
    }
}
