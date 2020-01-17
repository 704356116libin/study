<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PstExportPackage extends Model
{
    protected $fillable=[
        'name','user_id','company_id','export_template'
    ];
    protected $table='pst_export_package';
}
