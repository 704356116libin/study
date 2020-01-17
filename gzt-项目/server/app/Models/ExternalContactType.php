<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalContactType extends Model
{
    protected $table='external_contact_type';
    protected $fillable=['name','company_id'];

    /**
     *
     */
    public function externalGroupRelates()
    {
        return $this->morphMany(ExternalGroupRelate::class, 'model');
    }
}
