<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

/**
 * 基础信息模型
 * Class Basic
 * @package App\Models
 */
class Basic extends Model
{
   protected $table='basic';
   protected $fillable=['name','body'];
   protected $guard_name = 'gzt';//分组标识
   use HasRoles;//加入role*permission关系
}
