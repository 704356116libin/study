<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Permission\Exceptions\GuardDoesNotMatch;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Guard;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Traits\RefreshesPermissionCache;
use Venturecraft\Revisionable\Revision;
use Venturecraft\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * 角色模型类
 * Class Role
 * @package App\Models
 */
class Role extends Model implements RoleContract
{
    use HasPermissions;
    protected $fillable=['id','name','company_id','description','guard_name','sort','deleted_at'];
    protected $table='roles';
    public $timestamps=false;

//    protected $revisionCreationsEnabled = true;
//    protected $historyLimit = 10000;// 限制某个模型的记录数
//    protected $revisionCleanup = true;//日志记录达到上限自动清理
    /**
     * 角色/职位与权限的关系
     */
    public function permissions(): BelongsToMany
    {
        // TODO: Implement permissions() method.
        return $this->belongsToMany(
            config('permission.models.permission'),
            config('permission.table_names.role_has_permissions'),
            'role_id',
            'permission_id'
        );
    }
    /**
     * Find a role by its name and guard name.
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @return \Spatie\Permission\Contracts\Role
     *
     * @throws \Spatie\Permission\Exceptions\RoleDoesNotExist
     */
    public static function findByName(string $name, $guardName): RoleContract
    {
        // TODO: Implement findByName() method.
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $role = static::where('name', $name)->where('guard_name', $guardName)->first();

        if (! $role) {
            throw RoleDoesNotExist::named($name);
        }

        return $role;
    }

    /**
     * Find a role by its id and guard name.
     *
     * @param int $id
     * @param string|null $guardName
     *
     * @return \Spatie\Permission\Contracts\Role
     *
     * @throws \Spatie\Permission\Exceptions\RoleDoesNotExist
     */
    public static function findById(int $id, $guardName): RoleContract
    {
        // TODO: Implement findById() method.
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $role = static::where('id', $id)->where('guard_name', $guardName)->first();

        if (! $role) {
            throw RoleDoesNotExist::withId($id);
        }

        return $role;
    }

    /**
     * Find or create a role by its name and guard name.
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @return \Spatie\Permission\Contracts\Role
     */
    public static function findOrCreate(string $name, $guardName): RoleContract
    {
        // TODO: Implement findOrCreate() method.
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $role = static::where('name', $name)->where('guard_name', $guardName)->first();

        if (! $role) {
            return static::query()->create(['name' => $name, 'guard_name' => $guardName]);
        }

        return $role;
    }

    /**
     * Determine if the user may perform the given permission.
     *
     * @param string|\Spatie\Permission\Contracts\Permission $permission
     *
     * @return bool
     */
    public function hasPermissionTo($permission): bool
    {
        // TODO: Implement hasPermissionTo() method.
        $permissionClass = $this->getPermissionClass();

        if (is_string($permission)) {
            $permission = $permissionClass->findByName($permission, $this->getDefaultGuardName());
        }

        if (is_int($permission)) {
            $permission = $permissionClass->findById($permission, $this->getDefaultGuardName());
        }

        if (! $this->getGuardNames()->contains($permission->guard_name)) {
            throw GuardDoesNotMatch::create($permission->guard_name, $this->getGuardNames());
        }

        return $this->permissions->contains('id', $permission->id);
    }
    /**
     * 角色/职位与公司的关系
     */
    public function companies(): MorphToMany
    {
        return $this->morphedByMany(
            'App\Models\Company',
            'model',
            config('permission.table_names.model_has_roles'),
            'role_id',
            config('permission.column_names.model_morph_key')
        );
    }
    /**
     * 角色与用户的关系(可能开放单个用户的增值类服务)
     * @return MorphToMany
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            'App\Models\User',
            'model',
            config('permission.table_names.model_has_roles'),
            'role_id',
            config('permission.column_names.model_morph_key')
        );
    }
    /**
     * 部门管理者角色关系(多对多关系)
     */
    public function manage_department(){
        return $this->belongsToMany(Department::class,'company_department_manage_role','role_id','department_id');
    }

    /**
     * 角色和用户多对多关系
     */
    public function role_users()
    {
        return $this->belongsToMany(User::class,'company_user_role','role_id','user_id');
    }
    /**
     * 修改模型(记录)
     * 方法重写
     */
    public function postSave()
    {
        $user=auth('api')->user();
        if (isset($this->historyLimit) && $this->revisionHistory()->count() >= $this->historyLimit) {
            $LimitReached = true;
        } else {
            $LimitReached = false;
        }
        if (isset($this->revisionCleanup)){
            $RevisionCleanup=$this->revisionCleanup;
        }else{
            $RevisionCleanup=false;
        }

        // check if the model already exists
        if (((!isset($this->revisionEnabled) || $this->revisionEnabled) && $this->updating) && (!$LimitReached || $RevisionCleanup)) {
            // if it does, it means we're updating

            $changes_to_record = $this->changedRevisionableFields();
//            $model=Role::find($this->getKey());
            $field=config('companylog.model.role');//字段对应的名称array
            $revisions = array();
            $old_value = '';
            $new_value = '';
            $keyValue='';
            $model_type=config('companylog.model.role.type');
            foreach ($changes_to_record as $key => $change) {
                $old_value=$old_value.array_get($this->originalData, $key).'-';
                $new_value=$new_value.$this->updatedData[$key].'-';
                $keyValue=array_get($field,$key).'-';
            }
            $revisions[] = array(
                'revisionable_type' => $this->getMorphClass(),
                'revisionable_id' => $this->getKey(),
                'model_type'=>$model_type,
                'key' => $keyValue,
                'old_value' => $old_value,
                'new_value' => $new_value,
                'user_id' => $user->id,
                'user_name'=>$user->name,
                'company_id'=>$user->current_company_id,
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            );

            if (count($revisions) > 0) {
                if($LimitReached && $RevisionCleanup){
                    $toDelete = $this->revisionHistory()->orderBy('id','asc')->limit(count($revisions))->get();
                    foreach($toDelete as $delete){
                        $delete->delete();
                    }
                }
                $revision = new Revision;
                \DB::table($revision->getTable())->insert($revisions);
                \Event::fire('revisionable.saved', array('model' => $this, 'revisions' => $revisions));
            }
        }
    }
    /**
     * 创建模型(记录)
     * 方法重写
     */
    public function postCreate()
    {
        $user=auth('api')->user();
        // Check if we should store creations in our revision history
        // Set this value to true in your model if you want to
        if(empty($this->revisionCreationsEnabled))
        {
            // We should not store creations.
            return false;
        }

        if ((!isset($this->revisionEnabled) || $this->revisionEnabled))
        {
            $model=Role::find($this->getKey());
            $model_type=config('companylog.model.role,type');
            $revisions[] = array(
                'revisionable_type' => $this->getMorphClass(),
                'revisionable_id' => $this->getKey(),
                'model_type'=>$model_type,
                'key' => 'create_model',
                'old_value' => null,
                'new_value' => $model->name,
                'user_id' => $user->id,
                'user_name'=>$user->name,
                'company_id'=>$user->current_company_id,
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            );

            $revision = new Revision;
            DB::table($revision->getTable())->insert($revisions);
            \Event::fire('revisionable.created', array('model' => $this, 'revisions' => $revisions));
        }
    }
    /**
     * 删除模型(记录)
     * 方法重写
     */
    public function postDelete()
    {
        $user=auth('api')->user();
        if ((!isset($this->revisionEnabled) || $this->revisionEnabled)
            && $this->isSoftDelete()
            && $this->isRevisionable($this->getDeletedAtColumn())
        ) {
            $model_type=config('companylog.model.role,type');
            $model=Role::find($this->getKey());
            $revisions[] = array(
                'revisionable_type' => $this->getMorphClass(),
                'revisionable_id' => $this->getKey(),
                'model_type'=>$model_type,
                'key' => 'delete_model',
                'old_value' => $model->name,
                'new_value' => $this->{$this->getDeletedAtColumn()},
                'user_id' => $user->id,
                'user_name'=>$user->name,
                'company_id'=>$user->current_company_id,
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            );
            $revision = new \Venturecraft\Revisionable\Revision;
            \DB::table($revision->getTable())->insert($revisions);
            \Event::fire('revisionable.deleted', array('model' => $this, 'revisions' => $revisions));
        }
    }
}
