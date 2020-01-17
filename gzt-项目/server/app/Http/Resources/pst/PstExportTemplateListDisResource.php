<?php

namespace App\Http\Resources\pst;

use App\Tools\FunctionTool;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 评审通列表展示资源文件
 * Class PstTemplateResource
 * @package App\Http\Resources\pst
 */
class PstExportTemplateListDisResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        PstExportTemplateResource::$type_name=$this->name;
        return [
            'id'=>FunctionTool::encrypt_id($this->id),
            'name'=>$this->name,
            'count'=>$this->exportTems->where('is_show',0)->count(),
            'data'=>PstExportTemplateResource::collection($this->exportTems->where('is_show',0)),
            'created_at'=>Carbon::parse($this->created_at)->toDateTimeString(),
            'updated_at'=>Carbon::parse($this->updated_at)->toDateTimeString(),
        ];
    }
}
