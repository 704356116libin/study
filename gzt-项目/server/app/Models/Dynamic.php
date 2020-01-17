<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 动态模块模型类
 * Class Dynamic
 * @package App\Models
 */
class Dynamic extends Model
{
    protected $fillable=['user_id','list_info','unread_count'];
    protected $table='dynamic';

    /**
     * 与用户属于一对一关系
     */
    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
