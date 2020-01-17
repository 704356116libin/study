<?php

namespace App\Http\Resources;

use App\Tools\BusinessManagementTool;
use App\Tools\FunctionTool;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Repositories\CollaborativeRepository;
class CompanyUserResource extends JsonResource
{
    public static $company_id;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     * 用户在公司的个人信息
     */
    public function toArray($request)
    {
        return [
            'company_id'=>$this->company_id,
            'type'=>'user',
            'id'=>FunctionTool::encrypt_id($this->user_id),
            'name'=>$this->name,
            'email'=>$this->email,
            'tel'=>$this->tel,
            'avator'=>'http://gzts.oss-cn-beijing.aliyuncs.com/avators/cat.jpg',
            'gender'=>$this->sex,
            'roomNo'=>$this->roomNumber,
            'is_enable'=>BusinessManagementTool::freezeUser($this->id,self::$company_id),
        ];
    }
}
