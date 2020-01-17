<?php

namespace App\Http\Resources\user;

use App\Tools\BusinessManagementTool;
use App\Tools\FunctionTool;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * 用户在某个企业内的简略信息
 * Class UserSimpleResource
 * @package App\Http\Resources\user
 */
class UserSimpleResource extends JsonResource
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
        $avatar_path=$this->oss->root_path.'avatar/';
        $avatar=Storage::disk('oss')->allFiles($avatar_path);
        return [
            'company_id'=>self::$company_id,
            'type'=>'user',
            'id'=>FunctionTool::encrypt_id($this->id),
            'name'=>$this->name,
            'email'=>$this->email,
            'tel'=>$this->tel,
            'avator'=>'http://gzts.oss-cn-beijing.aliyuncs.com/avators/cat.jpg',
            'gender'=>'男女',
            'roomNo'=>'110120119',
            'user_info'=>[
                'id'=>FunctionTool::encrypt_id($this->id),
                'name'=>$this->name,
                'avatar'=>'https://gzts.oss-cn-beijing.aliyuncs.com/'.(count($avatar)===0?'没有可用头像':$avatar[0]),
            ],
            'is_enable'=>BusinessManagementTool::freezeUser($this->id,self::$company_id),
        ];
    }
}
