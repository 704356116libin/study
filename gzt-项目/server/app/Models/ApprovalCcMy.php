<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalCcMy extends Model
{
    protected $table = 'approval_cc_my';
    protected $fillable = ['user_id','approval_id','type_id','company_id'];

    /**
     * 此条抄送属于那个审批
     */
    public function approval()
    {
        return $this->belongsTo(Approval::class,'approval_id');
    }
}
