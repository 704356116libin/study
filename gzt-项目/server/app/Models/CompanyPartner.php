<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 企业合作伙伴关系类--此类没有什么大的作用,主要用于动态中推送消息进行资源文件映射
 * Class CompanyPartner
 * @package App\Models
 */
class CompanyPartner extends Model
{
    protected $fillable=['from_cid','to_cid','status'];
    public $timestamps=false;
    protected $table='company_partner';
    /**
     * 合作伙伴关系表与分组类型的多对多
     */
    public function partners()
    {
        return $this->belongsToMany(CompanyPartnerSort::class,'partner_sort','partner_id','sort_id');
    }
    /**
     * 与公司的一对一关系
     */
    public function company()
    {
        return $this->belongsTo(Company::class,'company_id');
    }
    /**
     * 与公司的一对一关系
     */
    public function inviteCompany()
    {
        return $this->belongsTo(Company::class,'invite_company_id');
    }
}
