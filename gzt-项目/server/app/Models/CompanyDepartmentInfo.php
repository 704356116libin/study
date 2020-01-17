<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 存储公司部门信息的模型类
 * Class CompanyDepartmentInfo
 * @package App\Models
 */
class CompanyDepartmentInfo extends Model
{
    protected $fillable=['info','company_id'];
    protected $table='company_department_info';
    public $timestamps=false;
}
