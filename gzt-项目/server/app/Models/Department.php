<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

/**
 * 公司/组织部门模型类
 * Class Department
 * @package App\Models
 */
class Department extends Model
{
    use NodeTrait;

    protected $table='company_department';
    public $timestamps=false;
    protected $fillable = [
        'company_id', 'description','name','parent_id'
    ];

    /**
     * 公司部门与用户的多对多关系
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(){
        return $this->belongsToMany(User::class,'user_department','department_id','user_id');
    }
    /**
     * 部门对应的管理角色(多对多关系)
     */
    public function manage_role(){
        return $this->belongsToMany(Department::class,'company_department_manage_role','department_id','role_id');
    }

    /**
     * 关联删除
     * $model 模型
     */
    public static function boot()
    {
        parent::boot();
        static::deleting(function ($model){
            return $model;
            //delete 对象
        });
    }
}
