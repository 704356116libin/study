<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $table = 'approval';
    protected $fillable = [
        'applicant', 'name', 'type_id', 'form_template', 'process_template', 'cc_my','archive_time','complete_time',
        'description', 'end_status', 'company_id', 'cancel_or_archive', 'numbering', 'approval_method', 'opinion','extra_data',
        'related_pst_id'
    ];

    /**
     * 审批包含的附件(文件)
     */
    public function files()
    {
        return $this->morphToMany(OssFile::class, 'model', 'model_has_file', 'model_id', 'file_id');
    }

    /**
     * 一个审批属于一个类型
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approvalType()
    {
        return $this->belongsTo(ApprovalType::class, 'type_id');
    }

    /**
     * 一个审批申请有多个人审批
     */
    public function approvalUsers()
    {
        return $this->hasMany(ApprovalUser::class, 'approval_id');
    }

    /**
     * 审批和用户的多对一关系
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'applicant');
    }

    /**
     * 用户与审批的抄送关系
     */
    public function cc_users()
    {
        return $this->belongsToMany(User::class, 'approval_cc_my', 'user_id', 'approval_id');
    }

    /**
     * 对应的抄送
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cc()
    {
        return $this->hasMany(ApprovalCcMy::class, 'approval_id');
    }
}
