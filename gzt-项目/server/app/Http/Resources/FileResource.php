<?php

namespace App\Http\Resources;

use App\Tools\FunctionTool;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FileResource extends JsonResource
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
            'name'=>$this->name,//文件名
            'oss_path'=>config('oss.root_path').$this->oss_path,//云路径
            'size'=>Storage::disk('oss')->size($this->oss_path),
            'lastModified'=>date('Y-m-d H:i:s',Storage::disk('oss')->lastModified($this->oss_path)),
            'type'=>'file',
        ];
    }
}
