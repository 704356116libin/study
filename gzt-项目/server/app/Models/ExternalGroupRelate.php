<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalGroupRelate extends Model
{
    protected $table='external_group_relate';
    protected $fillable=['model_id','model_type','external_id'];

    /**
     * 多对一(属于一个外部对相应关系)
     */
    public function externalContact()
    {
        return $this->belongsTo(CompanyExternalContact::class,'external_id');
    }
    /**
     * 远程一对多
     */
    public function users()
    {
        return $this->hasManyThrough(
            User::class,
            CompanyExternalContact::class,
            'id', // 第三个参数表示中间模型的外键名
            'id', // 第四个参数表示最终模型的外键名
            'external_id', // 第五个参数表示本地键名
            'external_contact_id' // 第六个参数表示中间模型的本地键名...
        );
    }

    /**
     * 与用户一对一
     */
    public function user()
    {
        return $this->belongsTo(User::class,'external_id');
    }
    /**
     * 与公司一对一
     */
    public function company()
    {
        return $this->belongsTo(Company::class,'external_id');
    }
}
