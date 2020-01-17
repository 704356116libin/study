<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Venturecraft\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
class ApprovalTemplate extends Model
{
    use RevisionableTrait;
    protected $table = 'approval_template';
    protected $fillable = ['name','form_template','process_template','type_id',
        'approval_method','company_id','is_show','numbering','description','per','cc_user',''];

//    protected $revisionCreationsEnabled = true;
//    protected $historyLimit = 1000000;// 限制某个模型的记录数
//    protected $revisionCleanup = true;//日志记录达到上限自动清理
    /**
     * 创建类型与审批模板的一对多关系
     */
    public function approvalType()
    {
        return $this->belongsTo(ApprovalType::class,'type_id');
    }
}
