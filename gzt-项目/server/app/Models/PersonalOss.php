<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 用户文件模型类
 * Class UserOss
 * @package App\Models
 */
class PersonalOss extends Model
{
    protected $fillable=['id','user_id','name','root_path','all_size','now_size'];
    protected $table='personal_oss';
    /**
     * 个人云存储关系(一对一)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
