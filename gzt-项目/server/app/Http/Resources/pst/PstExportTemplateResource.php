<?php

namespace App\Http\Resources\pst;

use App\Http\Resources\user\UserDetailResource;
use App\Models\User;
use App\Tools\FunctionTool;
use App\Tools\PstTool;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 评审通列表展示资源文件
 * Class PstTemplateResource
 * @package App\Http\Resources\pst
 */
class PstExportTemplateResource extends JsonResource
{
    public static $type_name;
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
            'name'=>$this->name,
            'type'=>['type_id'=>$this->type_id,'type_name'=>self::$type_name],
            'is_show'=>$this->is_show,
            'header'=>$this->header,
            'footer'=>$this->footer,
            'text'=>json_decode($this->text),
            'parameter'=>json_decode($this->parameter),
            'per'=>json_decode($this->per),
            'allow_user_names'=>json_decode($this->per)=='all'?['所有人可见']:PstTool::exportTemAbleUser(json_decode($this->per)->staffId),
            'description'=>$this->description,
            'created_at'=>Carbon::parse($this->created_at)->toDateTimeString(),
            'updated_at'=>Carbon::parse($this->updated_at)->toDateTimeString(),
        ];
    }
}
