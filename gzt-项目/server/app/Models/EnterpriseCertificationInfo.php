<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnterpriseCertificationInfo extends Model
{
    protected $fillable =[
        'id','name','abbreviation','number','logo_id','tel','type','district','industry','address','zip_code','fax','url','license_id','company_id','created_at','updated_at'
    ];
    protected $table='enterprise_certification_info';

    /**
     * 与公司表一对一关系
     */
    public function company()
    {
        return $this->belongsTo(Company::class,'company_id');
    }
}
