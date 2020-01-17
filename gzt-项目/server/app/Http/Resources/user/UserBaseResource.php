<?php

namespace App\Http\Resources\user;

use App\Tools\FunctionTool;
use App\Tools\UserTool;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 用户的基础信息与企业无关
 * Class UserSimpleResource
 * @package App\Http\Resources\user
 */
class UserBaseResource extends JsonResource
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
            'type' => 'user',
            'id' => FunctionTool::encrypt_id($this->id),
            'name' => $this->name,
            'email' => $this->email,
            'tel' => $this->tel,
            'signature'=>$this->signature,
            'avatar' => UserTool::getPersonalAvatar(),
            // 'avatar'=>'http://gzts.oss-cn-beijing.aliyuncs.com/avators/cat.jpg',
        ];
    }
}
