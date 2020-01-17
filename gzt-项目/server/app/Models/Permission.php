<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Spatie\Permission\Contracts\Permission as PerContract;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Traits\HasRoles;

/**
 * 权限模型类
 * Class Permission
 * @package App\Models
 */
class Permission extends Model implements PerContract
{
    use HasRoles;
    protected $fillable=['id','name','description','guard_name','per_sort_id'];
    protected $table='permissions';
    public $timestamps=false;
//    public function Role(){
//        return $this->belongsToMany(Role::class,'role_per','permission_id','role_id');
//    }


    /**
     *权限与角色的关系
     */
    public function roles(): BelongsToMany
    {
        // TODO: Implement roles() method.
        return $this->belongsToMany(
            config('permission.models.role'),
            config('permission.table_names.role_has_permissions'),
            'role_id',
            'permission_id'
        );
    }

    /**
     * 通过权限名查找权限
     */
    public static function findByName(string $name, $guardName): PerContract
    {
        // TODO: Implement findByName() method.
//        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $permission = static::getPermissions()->filter(function ($permission) use ($name, $guardName) {
            return $permission->name === $name && $permission->guard_name===$guardName;
        })->first();
        if (! $permission) {
            throw PermissionDoesNotExist::create($name, $guardName);
        }
        return $permission;
    }

    /**
     *通过权限id查找权限
     */
    public static function findById(int $id, $guardName): PerContract
    {
        // TODO: Implement findById() method.
        $permission = static::getPermissions()->filter(function ($permission) use ($id, $guardName) {
            return $permission->id === $id&& $permission->guard_name===$guardName;
        })->first();
        if (! $permission) {
            throw PermissionDoesNotExist::withId($id, $guardName);
        }

        return $permission;
    }

    /**
     *
     */
    public static function findOrCreate(string $name, $guardName): PerContract
    {
        // TODO: Implement findOrCreate() method.

    }

    /**
     * 拿到所有的权限
     */
    protected static function getPermissions(): Collection
    {
        return static ::all();
    }

    /**
     * 权限对应的分类
     */
    public function sort()
    {
        return $this->belongsTo(PerSort::class,'per_sort_id');
    }
}
