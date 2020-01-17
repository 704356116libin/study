<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyOperationLog extends Model
{
    protected $fillable=['module_type','terminal_equipment','operation_type','operator_id','content','company_id','create_time'];
    protected $table='company_operation_log';
}
