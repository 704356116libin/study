<?php

namespace App\Http\Resources\pst;

use App\Tools\FunctionTool;
use Illuminate\Http\Resources\Json\JsonResource;
class PstProcessTemplateTypeResource extends JsonResource
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
            'type'=>'type',
            'id'=>FunctionTool::encrypt_id($this->id),
            'name'=>$this->name,//类型名称
            'count'=>count($this->processTemplates->filter(function ($v){
                return  $v->is_show==1;
            })),
        ];
    }
}
