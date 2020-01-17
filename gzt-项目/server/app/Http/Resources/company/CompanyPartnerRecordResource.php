<?php

namespace App\Http\Resources\company;

use App\Models\Company;
use App\Repositories\CompanyRepository;
use App\Tools\FunctionTool;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 获取某个企业合作伙伴的邀请记录json
 */
class CompanyPartnerRecordResource extends JsonResource
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
            'type'=>'user',
            'id'=>FunctionTool::encrypt_id($this->id),
            'name'=>$this->name,
            'avator'=>'http://gzts.oss-cn-beijing.aliyuncs.com/avators/cat.jpg',
//            'email'=>$this->email,
//            'email_verified'=>$this->email_verified,
            'tel'=>$this->tel,
            'tel_verified'=>$this->tel_verified,
            'company_name'=>count($company_ids)==0?'暂无企业信息':Company::find($company_ids[0])->name,
        ];
    }
}
