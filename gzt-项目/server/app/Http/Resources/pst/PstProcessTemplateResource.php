<?php

namespace App\Http\Resources\pst;

use App\Repositories\PstRepository;
use App\Tools\FunctionTool;
use Illuminate\Http\Resources\Json\JsonResource;
class PstProcessTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $per=json_decode($this->per,true);
        if($per!='all'){
            $per['staffId']=FunctionTool::encrypt_id_array($per['staffId']);
        }
        return [
            'id'=>FunctionTool::encrypt_id($this->id),
            'name'=>$this->name,//模板名称
            'process_template'=>json_decode($this->process_template),//流程数据
            'description'=>$this->description,//描述
            'updated_at'=>date(config('basic.date_format'),strtotime($this->updated_at)),//更新时间
            'is_show'=>$this->is_show,//是否启用
            'type'=>[
                'id'=>FunctionTool::encrypt_id($this->processType->id),
                'name'=>$this->processType->name,
            ],
            'per'=>$per,
            'allow_user_names'=>PstRepository::getPerUserNames($this->per)//调出可见人信息
        ];
    }
}
