<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCompanyInfo extends Model
{
    protected $table='user_company_info';
    protected $fillable=[
        'user_id','company_id','name','sex','tel','email','role_ids','remarks','address','roomNumber','department_id','activation',
    ];

    /**
     * 与公司一对一
     */
    public function company()
    {
        return $this->belongsTo(Company::class,'company_id','');
    }
    /**
     * 与部门的一对一
     */
    public function department()
    {
        return $this->belongsTo(Department::class,'department_id');
    }
}
