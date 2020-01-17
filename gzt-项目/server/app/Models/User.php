<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * 用户模型类
 * Class User
 * @package App\Models
 */
class User extends Authenticatable
{
    use Notifiable,HasApiTokens, Notifiable,HasRoles;
    protected $guard_name = 'gzt';//分组标识
    public static $external_contact_status;//默认为
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email','email_verified', 'password','tel','tel_verified','email_token','current_company_id','signature'
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];
    /**
     * 修改passport的默认登陆字段email修改为tel
     */
    public function findForPassport($tel){
        return User::where('tel',$tel)->first();
    }
    /**
     * 用户与公司的多对多关系
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function company(){
        return $this->belongsToMany(Company::class,'user_company','user_id','company_id');
    }
    /**
     * 公司部门与用户的多对多关系
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function departments(){
        return $this->belongsToMany(Department::class,'user_department','user_id','department_id');
    }
    /**
     * 个人云存储关系(一对一)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function oss(){
        return $this->hasOne(PersonalOss::class,'user_id','id');
    }
    /**
     * 个人云存储关系(一对一)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function oss_files(){
        return $this->hasMany(PersonalOssFile::class,'user_id');
    }
    /**
     * 用户关注的企业公告
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function follow_notices(){
        return $this->belongsToMany(CompanyNotice::class,'user_notice_follow','user_id','notice_id');
    }
    /**
     * 用户负责的协作(一对多)
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function collaborativeTasks()
    {
        return $this->hasMany('App\Models\CollaborativeTask','principal_id');
    }
    /**
     * 用户的通知
     */
    public function notification(){
        return $this->hasMany(Notification::class,'user_id','id');
    }
    /**
     * 与审批申请yi对多
     */
    public function approvalUser()
    {
        return $this->hasMany(ApprovalUser::class,'approver_id','id');
    }
    /**
     * 一个用户拥有审批申请
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function approvals()
    {
        return $this->hasMany(Approval::class,'applicant');
    }
    /**
     * 我的抄送的(审批)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cc_approval()
    {
        return $this->belongsToMany(Approval::class,'approval_cc_my','user_id','approval_id');
    }
    /**
     * 用户与动态属于一对一
     */
    public function dynamic(){
        return $this->hasOne(Dynamic::class,'user_id','id');
    }
    /**
     * 角色和用户多对多关系
     */
    public function user_roles()
    {
        return $this->belongsToMany(Role::class,'company_user_role','user_id','role_id');
    }
    /**
     * 公司与外部联系人之间的多对多关系
     */
    public function externalContactCompanys()
    {
        return $this->belongsToMany(Company::class,'company_external_contact','external_contact_id','company_id')->wherePivot('status',self::$external_contact_status)->withPivot(['status','description']);
    }

    /**
     * 一个用户拥有订单
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class,'user_id');
    }

    /**
     * 一个用户拥有发票
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class,'user_id');
    }

    public function transfer()
    {
        return $this->hasMany(Transfer::class,'user_id');
    }
}
