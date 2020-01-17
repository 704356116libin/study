<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalCompanyGroup extends Model
{
    protected $table='external_company_group';
    protected $fillable=['name','user_id'];
    public function externalGroupRelates()
    {
        return $this->morphMany(ExternalGroupRelate::class, 'model');
    }
}
