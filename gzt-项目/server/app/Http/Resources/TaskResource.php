<?php

namespace App\Http\Resources;

use App\Tools\FunctionTool;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Repositories\CollaborativeRepository;
class TaskResource extends JsonResource
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
            'id'=>FunctionTool::encrypt_id($this->id),//任务id
            'title'=>$this->title,//标题
            'description'=>$this->description,//描述
            'formArea'=>json_decode($this->form_area),//表单内容
            'files'=>FileResource::collection($this->files),//文件地址
            'is_cancel'=>$this->is_delete,
            'assist_status'=>CollaborativeRepository::left_s($this),
            'identity'=>CollaborativeRepository::identity($this),//我的身份
            'my_status'=>CollaborativeRepository::s($this)['status'],//任务完成状态(只针对负责人任务栏,任务真正完成状态),
            'my_type'=>CollaborativeRepository::my_type($this->id),
            'edit_form'=>CollaborativeRepository::edit_form([$this->edit_form,[$this->initiate_id,$this->principal_id],$this->invitations->pluck('receive_user')->toArray()]),//能否编辑表单
            'limit_time'=>$this->limit_time,//任务限定完成时间
            'created_at'=>Carbon::parse($this->created_at)->toDateTimeString(),//创建时间
            'updated_at'=>Carbon::parse($this->updated_at)->toDateTimeString(),//更新时间
            'initiate'=>CollaborativeRepository::users([$this->initiate_id],$this->status,'发起者',$this->initiate_opinion,$this->review_time),//发起者id=>name
            'principal'=>CollaborativeRepository::users([$this->principal_id],$this->is_receive,'负责人',$this->principal_opinion,$this->complete_time),//负责人id=>name
            'participate'=>CollaborativeRepository::users($this->invitations->pluck('receive_user'),$this->id,'参与者',null,null),//参与者id=>name
            'participants'=>json_decode($this->participants),
        ];
    }
}
