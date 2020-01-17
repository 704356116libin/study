<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyVersion extends Model
{
    protected $fillable=['name','staff_number'];
    protected $table='company_version';
    public $timestamps=false;
}
