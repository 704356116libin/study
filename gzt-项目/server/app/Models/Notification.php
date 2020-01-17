<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 通知/动态模型类
 * Class Notification
 * @package App\Models
 */
class Notification extends Model
{
    protected $table='notifications';
    protected $fillable=['type','message','user_id','company_id','','model_id','model_type','readed','path','title','ws_pushed'];
    /**
     * 获得此条通知对应的模型。
     */
    public function model()
    {
        return $this->morphTo();
    }
}
