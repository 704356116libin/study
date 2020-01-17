<?php

namespace App\Http\Resources\company;

use App\Models\User;
use App\Tools\BusinessManagementTool;
use App\Tools\FunctionTool;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 企业or组织信息 json
 * Class CompanyNoticeResource
 * @package App\Http\Resources
 */
class CompanyUserInfoResource extends JsonResource
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
        return [
            'company_id'=>self::$company_id,
            'id'=>FunctionTool::encrypt_id($this->user_id),
            'name'=>$this->name,
            'email'=>$this->email,
            'tel'=>$this->tel,
            'avator'=>'http://gzts.oss-cn-beijing.aliyuncs.com/avators/cat.jpg',
            'sex'=>$this->sex,
            'roomNumber'=>$this->roomNumber,
            'activation'=>$this->activation,
            'is_enable'=>BusinessManagementTool::freezeUser($this->user_id,self::$company_id),
        ];
    }
}
