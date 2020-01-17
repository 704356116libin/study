<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalType extends Model
{
    protected $table = 'approval_type';
    protected $fillable = ['name','company_id','sequence'];

    /**
     * 创建类型与审批模板的一对多关系
     */
    public function templates()
    {
        return $this->hasMany(ApprovalTemplate::class,'type_id');
    }

    /**
     * 一种类型有多个审批
     */
    public function approvals()
    {
        return $this->hasMany(Approval::class,'type_id');
    }
}
