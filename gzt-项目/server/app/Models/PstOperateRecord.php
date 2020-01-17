<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 评审通日志操作模型类
 * Class PstOperateRecord
 * @package App\Models
 */
class PstOperateRecord extends Model
{
    protected $table='pst_operate_record';
    protected $fillable=['pst_id','company_id','type','operate_user_id','info'];
}
