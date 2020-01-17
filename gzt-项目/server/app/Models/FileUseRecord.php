<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileUseRecord extends Model
{
    protected $table='file_use_record';
    protected $fillable=['name','type','company_id','user_id','created_at','updated_at','path',];
    //对应个人文件
    public function user_file_id()
    {
        return $this->belongsTo(PersonalOssFile::class,'path','oss_path');
    }
    //对应企业文件
    public function company_file_id()
    {
        return $this->belongsTo(OssFile::class,'path','oss_path');
    }
}
