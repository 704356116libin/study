<?php

namespace App\Http\Resources\notice;

use App\Models\User;
use App\Repositories\CompanyNoticeBrowseRepository;
use App\Tools\CompanyNoticeTool;
use App\Tools\FunctionTool;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 企业公告列表json
 * Class CompanyNoticeResource
 * @package App\Http\Resources
 */
class CompanyNoticeDetailResource extends JsonResource
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
            'company_id'=>FunctionTool::encrypt_id($this->company_id),
            'title'=>$this->title,
            'type'=>$this->type,
            'c_notice_column_id'=>FunctionTool::encrypt_id($this->c_notice_column_id),
            'content'=>$this->content,
            'organiser'=>$this->organiser,
            'order'=>$this->order,
            'is_show'=>$this->is_show,
            'is_top'=>$this->is_top,
            'browse_count'=>$this->browse_count,
            'browse_user_count'=>CompanyNoticeBrowseRepository::getNoticeBrowseCount($this->id),
            'guard_json'=>json_decode($this->guard_json,true),
            'is_follow'=>CompanyNoticeTool::getCompanyNoticeTool()->checkUserFollow($this->id)?1:0,
            'allow_download'=>$this->allow_download,
            'created_at'=>date(config('basic.date_format'),strtotime($this->created_at)),
        ];
    }
}
