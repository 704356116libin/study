<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalUser extends Model
{
    protected $table = 'approval_user';
    protected $fillable =
        [
          'approval_id','approver_id','approval_method','approval_level','type','status','level_status','opinion','level_end_time','transferee_id','complete_time'
        ];

    /**
     * 一个审批有多条对应关系
     */
    public function approval()
    {
        return $this->belongsTo(Approval::class,'approval_id');
    }
    /**
     * 与用户的yi对多关系
     */
    public function user()
    {
        return $this->belongsTo(User::class,'approver_id');
    }
}
