<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyPartnerSort extends Model
{
    protected $fillable=['name'];
    protected $table='company_partner_sort';

    /**
     * 合作伙伴关系表与分组类型的多对多
     */
    public function partners()
    {
        return $this->belongsToMany(CompanyPartner::class,'partner_sort','sort_id','partner_id');
    }
}
