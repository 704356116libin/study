<?php

namespace App\Http\Resources\user;

use App\Models\Company;
use App\Models\User;
use App\Repositories\CompanyRepository;
use App\Tools\FunctionTool;
use App\Tools\UserTool;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * 用户在某个企业下的
 */
class UserCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //获取用户加入的首个company_id(暂时假定只取一个company的信息)
        $avatar_path=User::find($this->id)->oss->root_path.'avatar/';
        $avatar=Storage::disk('oss')->allFiles($avatar_path);
        return [
            'type'=>'user',
            'id'=>FunctionTool::encrypt_id($this->id),
            'name'=>$this->name,
            'avator'=>'https://gzts.oss-cn-beijing.aliyuncs.com/'.(count($avatar)===0?'没有可用头像':$avatar[0]),
            'signature'=>$this->signature,
            'tel'=>$this->tel,
            'tel_verified'=>$this->tel_verified,
//            'company_data'=>UserTool::getCompanyData($this->id),
            'company_data'=>UserTool::getNewCompanyData($this->id,$this->present_company_id),
        ];
    }
}
