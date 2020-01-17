<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PstExportType extends Model
{
    protected $fillable=[
        'name','company_id','sequence'
    ];
    protected $table='pst_export_type';
    public function exportTems()
    {
        return $this->hasMany(PstExportTemplate::class,'type_id');
    }
}
