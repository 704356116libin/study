<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PstTemplateType extends Model
{
    protected $table='pst_template_type';
    protected $fillable=['company_id','name','sequence','create_at','update_at'];

    /**
     * 评审通模板类型与评审通模板(一对多)
     */
    public function pstTemplates(){
        return $this->hasMany(PstTemplate::class,'type_id','id');
    }
}
