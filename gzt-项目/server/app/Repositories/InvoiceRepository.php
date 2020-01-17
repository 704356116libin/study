<?php
namespace App\Repositories;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

/**
 * 发票
 * Class InvoiceRepository
 * @package App\Repositories
 */
class InvoiceRepository
{
    private static $invoiceRepository;
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {

    }
    /**
     * 单例模式
     */
    static public function getInvoiceRepository(){
        if(self::$invoiceRepository instanceof self)
        {
            return self::$invoiceRepository;
        }else{
            return self::$invoiceRepository = new self;
        }
    }
    public function createUserInvoices($data)
    {
        return Invoice::create($data);
    }


    /**
     * 拿到用户所有的开票条数
     * @param $user_id
     * @return Invoice[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getUserAllInvoice($user_id)
    {
        return Invoice::where('user_id',$user_id)
            ->get();
    }


    /**
     * 根据条件获取用户的开票列表
     * @param $user_id 用户id
     * @param $pagesize 每页显示条数
     */
    public function getUserInvoiceList($user_id,$company_id,$pagesize)
    {
        return Invoice::where('user_id',$user_id)
            ->where('company_id',$company_id)
            ->orderBy('created_at','desc')

            ->paginate($pagesize);

    }

    /**
     * 拿到用户保存的所有发票头信息
     * @param $id
     * @return mixed
     */
    public function getUserAllInvoiceTitle($user_id,$company_id)
    {
        return DB::table('user_invoice_title')
            ->where('user_id',$user_id)
            ->where('company_id',$company_id)
            ->get();
    }

    /**
     * 拿到用户最新存储的发票头信息
     * @param $user_id
     * @return mixed
     */
    public function getUserLastInvoiceTitle($user_id)
    {
        return DB::table('user_invoice_title')
            ->where('user_id',$user_id)
            ->latest()
            ->first();
    }
    /**
     * 保存用户的发票头信息
     * @param $data
     * @return mixed
     */
    public function addUserInvoiceTitle($user_id,$data)
    {
        $invoice=$this->getUserAllInvoiceTitle($user_id);
        $invoice=$invoice===null?[]:$invoice;
        $count = count($invoice)?count($invoice):0;
        if($count){
            $data['default']=1;
            $id = DB::table('user_invoice_title')
                ->insertGetId($data);
            return $this->setUserDefaultInvoiceTitle($user_id,$id);
        }else{
            return DB::table('user_invoice_title')
                ->insert($data);
        }


    }

    /**
     * 拿到用户默认的发票头信息
     * @param $user_id
     * @return mixed
     */
    public function getUserDefaultInvoiceTitle($user_id,$company_id){
        return DB::table('user_invoice_title')
            ->where('user_id',$user_id)
            ->where('company_id',$company_id)
            ->where('default',1)
            ->first();
    }

    /**
     * 更新用户发票头信息
     * @param $id
     * @param $data
     * @return mixed
     */
    public function updateUserInvoiceTitle($id,$data)
    {
        return DB::table('user_invoice_title')
            ->where('id',$id)
            ->update($data);
    }

    /**
     * 转换用户默认发票头
     * @param $user_id
     * @param $id
     */
    public function setUserDefaultInvoiceTitle($user_id,$id)
    {
        return DB::table('user_invoice_title')
            ->where('user_id',$user_id)
            ->where('id','!=',$id)
            ->update(['default'=>0]);
    }

    /**
     * 删除用户默认发票头
     * @param $ids
     */
    public function deleteUserInvoiceTitle($ids)
    {
        return DB::table('user_invoice_title')->where('id',$ids)->delete();
    }

    /**
     * 获取用户未处理的发票申请
     * @param $user_id
     */
    public function getUnsetInvoice($user_id)
    {
        return Invoice::where('state','!=',2)
            ->where('state','!=',0)
            ->where('user_id',$user_id)
            ->get();
    }

    /**
     * 更新开票状态
     * @param $invoice_id
     * @param $state
     * @return bool
     */
    public function setUserInvoiceState($invoice_id,$state)
    {
        return Invoice::where('id',$invoice_id)
            ->update(['state'=>$state]);
    }



}
