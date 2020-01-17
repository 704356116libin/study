<?php

namespace App\Http\Resources\dynamic;

use App\Tools\CompanyNoticeTool;
use App\Tools\FunctionTool;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 企业公告--通知的json
 */
class CollaborativeTask extends JsonResource
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
            'status'=>$this->status,
            'description'=>$this->description,
            'created_at'=>Carbon::parse($this->created_at)->toDateTimeString(),
            'limit_time'=>$this->limit_time,
        ];
    }
}
