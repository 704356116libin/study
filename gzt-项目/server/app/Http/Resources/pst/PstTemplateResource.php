<?php

namespace App\Http\Resources\pst;

use App\Repositories\PstRepository;
use App\Tools\FunctionTool;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 评审通模板资源文件
 * Class PstTemplateResource
 * @package App\Http\Resources\pst
 */
class PstTemplateResource extends JsonResource
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
        if(($per != 'all')&&empty($per)){
            $per['staffId']=FunctionTool::encrypt_id_array($per['staffId']);
        }
        return [
            'id'=>FunctionTool::encrypt_id($this->id),
            'company_id'=>FunctionTool::encrypt_id($this->company_id),
            'name'=>$this->name,//模板名称
            'description'=>$this->description,//描述
            'need_approval'=>$this->need_approval==1?true:false,//是否需要审批
            'approval_method'=>$this->approval_method,//审批方法
            'process_template'=>json_decode($this->process_template),//流程数据
            'form_template'=>json_decode($this->form_template),
            'per'=>$per,
            'cc_users'=>json_decode($this->cc_users),
            'is_show'=>$this->is_show,//是否启用
            'type'=>[
                'id'=>FunctionTool::encrypt_id($this->pstType->id),
                'name'=>$this->pstType->name,
            ],
            'updated_at'=>date(config('basic.date_format'),strtotime($this->updated_at)),//更新时间
            'allow_user_names'=>PstRepository::getPerUserNames($this->per)//调出可见人信息
        ];
    }
}
