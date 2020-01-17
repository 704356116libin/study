<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 评审通流程类型model
 * Class PstProcessTemplateType
 * @package App\Models
 */
class PstProcessTemplateType extends Model
{
    protected $table='pst_process_type';
    protected $fillable=['company_id','name','sequence'];
    public $timestamps=true;
    /**
     * 过程模板类型与过程模板(一对多)
     */
    public function processTemplates(){
        return $this->hasMany(PstProcessTemplate::class,'process_type_id','id');
    }

}
