<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 评审通模型类
 * Class Pst
 * @package App\Models
 */
class Pst extends Model
{
    protected $table='pst';
    protected $fillable=['last_pst_id','template_id','publish_user_id','company_id','outside_user_id','state',
                    'need_approval','current_handlers','removed','form_template','form_values','process_template','approval_method','origin_data',
                    'join_user_data','join_pst_form_data','duty_user_data','transfer_duty_data','cc_user_data','finish_form'];

//    /**
//     * 这个属性应该被转换为原生类型.
//     *
//     * @var array
//     */
//    protected $casts = [
//        'form_template' => 'json',
//        'form_values' => 'json',
//        'process_template' => 'json',
//        'origin_data' => 'json',
//        'join_user_data' => 'json',
//        'join_pst_form_data' => 'json',
//        'transfer_join_data' => 'json',
//        'cc_user_data' => 'json',
//        'duty_user_data' => 'json',
//        'transfer_duty_data' => 'json',
//        'current_handlers' => 'json',
//    ];
    /**
     * 协助与评审通一对多关系
     */
    public function collaborativeTasks()
    {
        return $this->hasMany(CollaborativeTask::class,'pst_id','id');
    }
    /**
     * 公告包含的附件(文件)
     */
    public function files(){
        return $this->morphToMany(OssFile::class, 'model','model_has_file','model_id','file_id');
    }
}
