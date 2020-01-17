<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * 公告栏目模型类
 * Class CompanyNoticeColumn
 * @package App\Models
 */
class CompanyNoticeColumn extends Model implements Sortable
{
    use SortableTrait;
    protected $fillable=['id','company_id','name','description','order'];
    protected $table='company_notice_column';
    public $timestamps=false;
    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];
    /**
     * 与企业多对一
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(){
        return $this->belongsTo(Company::class,'company_id');
    }
    /**
     *与企业公告一对多
     */
    public function cnotice(){
        return $this->hasMany(CompanyNotice::class,'c_notice_column_id');
    }
}
