<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 评审通流程模板类
 * Class PstProcessTemplate
 * @package App\Models
 */
class PstProcessTemplate extends Model
{
    protected $table='pst_process_template';
    protected $fillable=['company_id','process_type_id','name','is_show','process_template','per','description'];
    public $timestamps=true;
    /**
     * 过程模板类型与过程模板(一对多)
     */
    public function processType(){
        return $this->belongsTo(PstProcessTemplateType::class,'process_type_id','id');
    }
}
