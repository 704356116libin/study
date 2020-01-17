<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 发票模型类
 * Class Invoice
 * @package App\Models
 */

class Invoice extends Model
{
    const INVOICE_STATUS_SUCCESS = '2';
    const INVOICE_STATUS_PROCESSING = '1';
//    const INVOICE_STATUS_FAILED = 'failed';

    const INVOICE_TYPE_PERSONAL = 'personal';
    const INVOICE_TYPE_BUSINESS = 'business';

    public $invoiceTitleType = [
        self::INVOICE_TYPE_PERSONAL =>'个人',
        self::INVOICE_TYPE_BUSINESS =>'企业'
    ];
    public $invoiceStatus = [
        self::INVOICE_STATUS_SUCCESS =>'已开票',
//        self::INVOICE_STATUS_FAILED =>'未开票',
        self::INVOICE_STATUS_PROCESSING=>'处理中'
    ];
    protected $table = 'invoices';
    protected $fillable = [
        'user_id',
        'company_id',
        'order_id',
        'state',
        'inv_type',
        'inv_title',
        'reg_address',
        'reg_phone',
        'money',
        'bank_of_deposit',
        'bank_account',
        'ratepayer_number',
        'inv_demand',
        'user_phone',
        'remark'
    ];




//    public function user()
//    {
//        return $this->belongsTo(User::class);
//    }
//
    public function company()
    {
        return $this->belongsTo(Company::class);
    }


}