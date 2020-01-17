<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 企业合作伙伴邀请记录bean对象
 * Class CompanyPartnerRecord
 * @package App\Models
 */
class CompanyPartnerRecord extends Model
{
    protected $table='company_partner_record';
    protected $fillable=['company_id','operate_user_id','invite_company_id','invite_company_name','state','apply_description'];
    public function user()
    {
        return $this->belongsTo(User::class,'operate_user_id','id');
    }
}
