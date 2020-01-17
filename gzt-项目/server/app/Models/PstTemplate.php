<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PstTemplate extends Model
{
    protected $table='pst_template';
    protected $fillable=['name','type_id','company_id','is_show','need_approval','form_template','form_values','process_template'
            ,'approval_method','per','cc_users','users_info','description'];
    public $timestamps=true;
    /**
     * 过程模板类型与过程模板(一对多)
     */
    public function pstType(){
        return $this->belongsTo(PstTemplateType::class,'type_id','id');
    }
}
