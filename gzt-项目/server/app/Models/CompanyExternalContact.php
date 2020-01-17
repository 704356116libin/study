<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyExternalContact extends Model
{
    protected $fillable=['company_id','external_contact_id','status','description'];
    protected $table='company_external_contact';
}
