<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * 企业公告模型类
 * Class CompanyNotice
 * @package App\Models
 */
class CompanyNotice extends Model implements Sortable
{
//    use SortableTrait {
//        scopeOrdered as SortableScopeOrdered;
//    }
    use SortableTrait;
    protected $fillable=['id','company_id','title','content','type','organiser','is_show','is_top','is_draft'
                        ,'is_top','notified','allow_download','allow_user','guard_json','c_notice_column_id'];
    protected $table='company_notice';
    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];
//    public function scopeOrdered($query)
//    {
//        return $this->SortableScopeOrdered($query);
//    }
    /**
     * 与企业公告栏目多对一
     */
    public function cNoticeColumn(){
        return $this->belongsTo(CompanyNoticeColumn::class,'c_notice_cloumn_id');
    }
    /**
     * 公告包含的附件(文件)
     */
    public function files(){
        return $this->morphToMany(OssFile::class, 'model','model_has_file','model_id','file_id');
    }
    /**
     * 公告浏览用户
     */
    public function look_users(){
        return $this->belongsToMany(User::class,'company_notice_look_record','notice_id','user_id')
            ->as('look')
            ->withPivot('time');
    }
    /**
     *某公告对应的通知
     */
    public function notify()
    {
        return $this->morphMany(Notification::class, 'model');
    }
}
