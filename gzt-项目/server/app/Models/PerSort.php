<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerSort extends Model
{
    protected $fillable=['name','description'];
    protected $table='per_sort';
    /**
     * 与基础权限的一对多关系
     */
    public function pers()
    {
        return $this->hasMany(Permission::class,'per_sort_id');
    }
}
