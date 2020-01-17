<?php

namespace App\Http\Resources\dynamic;

use App\Tools\CompanyNoticeTool;
use App\Tools\FunctionTool;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 企业公告--通知的json
 */
class CompanyNotice extends JsonResource
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
            'id'=>FunctionTool::encrypt_id($this->id),
            'title'=>$this->title,
            'type'=>$this->type,
            'company_id'=>FunctionTool::encrypt_id($this->company_id),
            'organiser'=>$this->organiser,
            'time'=>date(config('basic.date_format'),strtotime($this->created_at)),
        ];
    }
}
