<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OssRecord extends Model
{
    protected $fillable=[
        'company_id','user_id','content','type','size','file_name','dir'
    ];
    protected $table='company_oss_record';
    public function company()
    {
        return $this->belongsTo(Company::class,'company_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
