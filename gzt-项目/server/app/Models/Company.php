<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Permission\Traits\HasRoles;

/**
 * 公司模型
 * Class Company
 * @package App\Models
 */
class Company extends Model
{
    protected $fillable=['name','creator_id','verified','email_count','sms_count','abbreviation','number','logo_id','tel','type','district','industry','address','zip_code','fax','url','license_id'];
    protected $table='company';
    protected $guard_name = 'gzt';//分组标识
    use HasRoles;

    /**
     * 企业下的员工信息(多对多)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(){
        return $this->belongsToMany(User::class,'user_company','company_id','user_id');
    }
    /**
     * 企业创建人关系
     */
    public function creator(){
        return $this->belongsTo(User::class,'creator_id','id');
    }
    /**
     * 公司公告关系(一对多)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function notices(){
        return $this->hasMany('App\Models\CompanyNotice','company_id');
    }
    /**
     * 公司云存储关系(一对一)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function oss(){
        return $this->hasOne(CompanyOss::class,'company_id');
    }
    /**
     * 公司云存储关系(一对一)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function oss_files(){
        return $this->hasMany(OssFile::class,'company_id');
    }
    /**
     * 企业与公告栏目的关系
     */
    public function notice_column(){
        return $this->hasMany(Compan);
    }
    /**
     * 公司与存储部门信息的一对一关系
     */
    public function department_json(){
        return $this->hasOne(CompanyDepartmentInfo::class,'company_id','id');
    }

    /**
     * 企业合作伙伴邀请记录(一对多关系)
     */
    public function partner_record(){
        return $this->hasMany(CompanyPartnerRecord::class,'company_id','id');
    }
    /**
     * 公司与合作伙伴(公司)的多对多
     */
    public function comapny_partner()
    {
        return $this->belongsToMany(Company::class,'company_partner','company_id','invite_company_id')->withPivot('status');
    }
    /**
     * 公司与外部联系人之间的多对多关系
     */
    public function externalContactUsers()
    {
        return $this->belongsToMany(User::class,'company_external_contact','company_id','external_contact_id')->wherePivot('status','=',1)->withPivot('status');
    }
    /**
     * 公司与公司详情表的一对一关系
     */
    public function companyDetail()
    {
        return $this->hasOne(EnterpriseCertificationInfo::class,'company_id');
    }
    /**
     * 公司与日志的一对多关系
     */
    public function operationLogs()
    {
        return $this->hasMany(CompanyOperationLog::class,'company_id');
    }
    /**
     * 公司认证文件关系
     */
    public function companyLicense()
    {
        return $this->hasOne(CompanyLicense::class,'company_id','id');
    }
    /**
     * 公司拥有的功能模块
     */
    public function funs()
    {
        return $this->hasMany('App\\Models\\CompanyHasFun','company_id');
    }
    /**
     * 公司拥有订单
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class,'user_id');
    }
    /**
     * 公司基础限定数据
     */
    public function companyBasisLimit()
    {
        return $this->hasMany(CompanyBasisLimit::class,'company_id');
    }
    /**
     * 公司拥有转账
     */
    public function transfer()
    {
        return $this->hasMany(Transfer::class,'company_id');
    }
}
