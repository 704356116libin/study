<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyHasFun extends Model
{
    protected $fillable =['per_sort_id','company_id','is_enable'];
    protected $table ='company_has_fun';
    public function per_sort()
    {
        return $this->hasOne(PerSort::class,'id','per_sort_id');
    }
}
