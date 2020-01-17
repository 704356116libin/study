<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 企业云模型类
 * Class CompanyOss
 * @package App\Models
 */
class CompanyOss extends Model
{
    protected $fillable=['id','company_id','name','root_path','all_size','now_size','expire_date'];
    protected $table='company_oss';
    /**
     * 公司云存储关系(一对一)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function company(){
        return $this->belongsTo(Company::class,'company_id','id');
    }
}
