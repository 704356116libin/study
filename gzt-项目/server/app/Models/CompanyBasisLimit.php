<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyBasisLimit extends Model
{
    protected $fillable=[
        'company_id','expire_date','type','type_number','user_number',
    ];
    protected $table='company_basis_limit';
}
