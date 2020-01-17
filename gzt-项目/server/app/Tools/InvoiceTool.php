<?php
namespace App\Tools;

use App\Interfaces\InvocieInterface;
use App\Models\Invoice;
use App\Models\Order;
use App\Repositories\InvoiceRepository;
use Illuminate\Support\Facades\DB;

class InvoiceTool implements InvocieInterface
{
    public static $invoiceTool;
    private $invoiceRepository;

    public function __construct()
    {
        $this->invoiceRepository = InvoiceRepository::getInvoiceRepository();
    }


    /**
     * 单例模式,实例化自身类并返回
     */
    public static function getInvoiceTool()
    {
        if (self::$invoiceTool instanceof self) {
            return self::$invoiceTool;
        } else {
            return self::$invoiceTool = new self();
        }
    }
    /**
     * 获取开票记录
     * @param $user_id
     */
    public function getInvoiceList($request)
    {
        $list = $this->invoiceRepository->getUserInvoiceList($request->user_id,$request->company_id,15);
        if(!empty($list)){
            return json_encode(['status'=>'success','list'=>$list,'message'=>'']);
        }else{
            return json_encode(['status'=>'fail','list'=>'','message'=>'暂无开票记录']);
        }

    }


    /**
     * 获取需要开票的订单列表
     * @param $request
     */
    public function getOrderList($request)
    {
        $order = Order::where('user_id',$request->user_id)
            ->where('company_id',$request->company_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $invoices = $this->invoiceRepository->getUserAllInvoice($request->user_id);
        $index = 0;
        $orders = [];
        foreach ($order as $i){
            if($this->checkOrder($invoices,$i->id)){
                $orders[$index] = $i;
                $index++;
            }

        }
        if(!empty($orders)){
            return json_encode(['status'=>'success','orders'=>$orders,'message'=>'']);
        }else{
            return json_encode(['status'=>'fail','orders'=>'','message'=>'暂无可开票订单']);
        }

    }


    /**
     * 获取用户保存的所有发票头
     * @param $user_id
     * @return \Illuminate\Support\Collection|mixed
     */
    public function getAllInvoiceTitle($request)
    {

            $userTitles = $this->invoiceRepository->getUserAllInvoiceTitle($request->user_id,$request->company_id);
        if($userTitles){
            return json_encode(['status'=>'success','userTitles'=>$userTitles,'message'=>'']);
        }else{
            return json_encode(['status'=>'fail','userTitles'=>'','message'=>'暂无发票头']);
        }
    }

    /**
     * 获取用户的默认发票头
     * @param $user_id
     * @return mixed
     */
    public function getDefaultInvoiceTitle($request)
    {
        $defTitle = $this->invoiceRepository->getUserDefaultInvoiceTitle($request->user_id,$request->company_id);
        if($defTitle){
            return json_encode(['status'=>'success','defTitle'=>get_object_vars($defTitle),'message'=>'']);
        }else{
            return json_encode(['status'=>'fail','defTitle'=>'','message'=>'用户未设置默认发票头']);
        }

//        return get_object_vars($defTitle);
    }


    /**
     * 保存用户发票抬头信息
     * @param $result
     * @param $user_id
     * @return array
     */
    public function saveInvoiceTitle($result,$user_id){

        if(count($this->invoiceRepository->getUserAllInvoice($user_id))>5)
        {
            return json_encode(['status'=>'fail','message'=>'最多只能保存5条记录!']);
        }
        $bool = false;
        if($result->update)
        {
            $bool = $this->invoiceRepository->updateUserInvoiceTitle($result->id,[
                'user_id'=>$user_id,
                'company_id'=>$result->company_id,
                'inv_type'=>$result->inv_type,
                'inv_title'=>$result->inv_title,
                'user_phone'=>$result->user_phone,
                'ratepayer_number'=>$result->ratepayer_number,
                'reg_address'=>$result->reg_address,
                'reg_phone'=>$result->reg_phone,
                'bank_of_deposit'=>$result->bank_of_deposit,
                'bank_account'=>$result->bank_account
            ]);
        }else{
            $bool = $this->invoiceRepository->addUserInvoiceTitle($user_id,[
                'user_id'=>$user_id,
                'company_id'=>$result->company_id,
                'inv_type'=>$result->inv_type,
                'inv_title'=>$result->inv_title,
                'user_phone'=>$result->user_phone,
                'ratepayer_number'=>$result->ratepayer_number,
                'reg_address'=>$result->reg_address,
                'reg_phone'=>$result->reg_phone,
                'bank_of_deposit'=>$result->bank_of_deposit,
                'bank_account'=>$result->bank_account
            ]);
        }
        if($bool)
        {
            return json_encode(['status'=>'success','message'=>'保存成功']);
        }else{
            return json_encode(['status'=>'fail','message'=>'保存失败,请稍后尝试!']);
        }
    }

    /**
     * 保存用户得发票信息
     * @param $result
     * @param $user_id
     * @return array
     */
    public function saveUserInvoiceMsg($result)
    {

        $invoice_data = $this->invoiceRepository->getUserDefaultInvoiceTitle($result->user_id);

        if(empty($invoice_data)){
            return json_encode(['status'=>'fail','message'=>'保存失败,请设置默认发票头!']);
        }
        $bool = $this->invoiceRepository->createUserInvoices([
            'user_id'=>$result->user_id,
            'company_id'=>$result->company_id,
            'money'=>$this->getOrderMoney(explode('|',$result->order_id)),
            'order_id'=>$result->order_id,
            'inv_type'=>$invoice_data->inv_type,
            'inv_title'=>$invoice_data->inv_title,
            'reg_address'=>$invoice_data->reg_address,
            'ratepayer_number'=>$invoice_data->ratepayer_number,
            'reg_phone'=>$invoice_data->reg_phone,
            'user_phone'=>$invoice_data->user_phone,
            'bank_of_deposit'=>$invoice_data->bank_of_deposit,
            'bank_account'=>$invoice_data->bank_account,
            'inv_demand'=>$result->inv_demand
        ]);
        if($bool)
        {
            return json_encode(['status'=>'success','message'=>'保存成功']);
        }else{
            return json_encode(['status'=>'fail','message'=>'保存失败,请稍后尝试!']);
        }
    }


    /**
     * 获取发票的详细（包括订单信息）
     * @param $invoice_id
     * @return mixed
     */
    public function getInvoiceDetail($invoice_id)
    {
        $dail['invoice']  = $invoice= Invoice::where('id',$invoice_id)->get();
        $dail['orders'] = $this->getInvoiceOrder(explode('|',$invoice[0]['order_id']));

        return json_encode(['status'=>'success','detail'=>$dail]);
    }

    /**
     * 获取发票下的订单
     * @param $ids
     * @return mixed
     */
    public function getInvoiceOrder($ids)
    {
        $orders = Order::whereIn('id',$ids)->get();
        return $orders;
    }

    /**
     * 修改用户开票状态
     * @param $invoce_id
     * @param $state
     * @return array
     */
    public function updateUserInvoiceState($invoce_id,$state)
    {
        $invoices = Invoice::where('id',$invoce_id)->get();
        if($invoices){
            $bool = $this->invoiceRepository->setUserInvoiceState($invoce_id,$state);
            if($bool){
                return json_encode(['status'=>'success','message'=>'保存成功']);
            }
        }
        return json_encode(['status'=>'fail','message'=>'保存失败,请稍后尝试!']);
    }

    /**
     * 获取指定订单的总价
     * @param $ids
     * @return int
     */
    public function getOrderMoney($ids)
    {
        $total_amount = Order::whereIn('id',$ids)->sum('total_amount');

        return $total_amount;
    }

    /**
     * 设置用户默认发票头
     * @param $user_id
     * @param $id
     * @return array
     */
    public function setDefaultInvoiceTitle($user_id,$id)
    {
        DB::beginTransaction();
        try
        {
            $this->invoiceRepository->updateUserInvoiceTitle($id,['default'=>1]);
            $this->invoiceRepository->setUserDefaultInvoiceTitle($user_id,$id);
            DB::commit();
            return json_encode(['status'=>'success','message'=>'设置成功']);
        }catch(\Exception $e)
        {
            DB::rollback();
            return json_encode(['status'=>'fail','message'=>'设置失败,稍后再试!']);
        }
    }
    /**
     * 删除用户发票头
     * @param $ids
     */
    public function deleteUserInvoiceTitle($ids)
    {
        $result = $this->invoiceRepository->deleteUserInvoiceTitle($ids);
        if($result)
        {
            return json_encode(['status'=>'success','message'=>'删除成功']);
        }else{
            return json_encode(['status'=>'fail','message'=>'删除失败,稍后再试!']);
        }
    }

    /**
     * 检测订单是否处于开票状态或者已经开票
     * @param $invoices
     * @param $order_id
     */
    public function checkOrder($invoices,$order_id)
    {
        $state = true;
        foreach ($invoices as $d)
        {
            $ids = explode('|',$d->order_id);
            if($d->state == 1 || $d->state == 2)
            {
                foreach($ids as $i)
                {
                    if($i==$order_id)
                    {
                        $state = false;
                        break;
                    }
                }
            }

        }
        return $state;
    }



}