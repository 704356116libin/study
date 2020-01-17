<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollaborativeTask extends Model
{
    protected $table = 'collaborative_task';
    protected $fillable = [
        'title', 'description', 'form_area', 'status', 'initiate_id','review_time','complete_time',
        'principal_id', 'edit_form', 'limit_time', 'difference', 'is_delete',
        'is_receive', 'form_edit', 'form_people', 'company_id', 'initiate_opinion', 'principal_opinion','pst_id','participants'
        ,'zj_principal_id','zj_reason'
    ];

    /**
     * 审批包含的附件(文件)
     */
    public function files()
    {
        return $this->morphToMany(OssFile::class, 'model', 'model_has_file', 'model_id', 'file_id');
    }

    /**
     * 协作任务和被邀请者的一对多关系
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invitations()
    {
        return $this->hasMany('App\Models\CollaborationInvitation');
    }

    /**
     * 多个协作任务属于一个负责人(一对多)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\user', 'principal_id');
    }
    /**
     * 协助与评审通一对多关系
     */
    public function pst()
    {
        return $this->belongsTo(Pst::class,'pst_id','id');
    }

}
