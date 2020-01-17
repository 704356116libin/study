<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalOssFile extends Model
{
    protected $fillable=['id','uploader_id','user_id','name','oss_path','size'];
    protected $table='personal_oss_file';

    /**
     * 文件与上传者的关系(一对一)
     */
    public function uploader(){
        return $this->hasOne(User::class,'uploader_id','id');
    }
}
