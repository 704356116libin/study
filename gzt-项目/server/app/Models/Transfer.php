<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 转账记录模型类
 * Class Transfer
 * @package App\Models
 */
class Transfer extends Model
{
    const TRANSFER_STATUS_UNACCOUNT = '1';
    const TRANSFER_STATUS_ARRIVED = '2';

    protected $table = 'transfers';
    protected $fillable = [
        'user_id',
        'order_id',
        'state',
        'company_id',
        'money'
    ];
    public static $transferState = [
        self::TRANSFER_STATUS_UNACCOUNT =>'未到账',
        self::TRANSFER_STATUS_ARRIVED =>'已到账'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }


}