<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyLogo extends Model
{
    protected $table='company_logo';
    protected $fillable=['company_id'];

    /**
     * 企业的认证文件(文件)
     */
    public function files()
    {
        return $this->morphToMany(OssFile::class, 'model', 'model_has_file', 'model_id', 'file_id');
    }
}
