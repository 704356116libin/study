<?php

namespace App\Http\Resources\pst;

use App\Http\Resources\user\UserBaseResource;
use App\Http\Resources\user\UserDetailResource;
use App\Models\User;
use App\Tools\FunctionTool;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * 评审通操作记录资源类
 * Class PstTemplateResource
 * @package App\Http\Resources\pst
 */
class PstRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user=User::find($this->operate_user_id);
        $avatar_path=$user->oss->root_path.'avatar/';
        $avatar=Storage::disk('oss')->allFiles($avatar_path);
        return [
           'pst_id'=>$this->pst_id,
           'company_id'=>$this->company_id,
           'type'=>$this->type,//操作类型
           'operate_user_id'=>$this->operate_user_id,//操作人id
           'info'=>$this->info,//操作详情信息
           'user_info'=>[
               'id'=>FunctionTool::encrypt_id($user->id),
               'name'=>$user->name,
               'avator'=>(count($avatar)===0?'没有可用头像':'https://gzts.oss-cn-beijing.aliyuncs.com/'.$avatar[0]),
           ],
           'created_at'=>date(config('basic.date_format'),strtotime($this->created_at)),//创建时间
        ];
    }
}
