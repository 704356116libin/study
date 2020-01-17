<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 云文件model类
 * Class OssFile
 * @package App\Models
 */
class OssFile extends Model
{
    protected $fillable=['id','uploader_id','company_id','name','oss_path','size'];
    protected $table='oss_file';

    /**
     * 文件与上传者的关系(一对一)
     */
    public function uploader(){
        return $this->hasOne(User::class,'uploader_id','id');
    }
}
