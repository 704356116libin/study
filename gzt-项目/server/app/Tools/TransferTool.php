<?php
namespace App\Tools;

use App\Http\Controllers\PaymentController;
use App\Models\Order;
use App\Repositories\TransferRepository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransferTool
{
    private $transferRepository;
    public function __construct()
    {
        $this->transferRepository = new TransferRepository();
    }

    /**
     * 获取转账列表
     * @return mixed
     */
    public function transferList()
    {
        return $this->transferRepository->transferList(15);
    }

    /**
     * 获取转账详情
     * @param $id
     * @return mixed
     */
    public function getTransfer($id){
        $detail = $this->transferRepository->getTransfer($id);
        $detail['orders'] = Order::where('id',$detail->order_id)->first();
        return $detail;
    }



    /**
     * 设置转账成功状态
     * @param $id
     * @param $data
     * @return mixed
     */
    public function setTransfer($id,$data)
    {
        DB::beginTransaction();
        try
        {
            $transfer = $this->transferRepository->getTransfer($id);
            $order = Order::where('id',$transfer->order_id)->first();
            //判断订单是否存在（一般情况下 订单都存在 增强系统稳固性）
            if($order){
                $this->setOrderState($transfer->order_id);
                $this->transferRepository->saveTransfer($id,$data);
                $pay = new PaymentController();
                $pay->transferNotify($transfer->order_id);
            }else{
                $this->transferRepository->saveTransfer($id,$data);
            }
            DB::commit();
            return ['status'=>'success','message'=>'设置成功'];
        }catch(\Exception $e) {
            DB::rollback();
            return ['status'=>'fail','message'=>'设置失败,稍后再试!'];
        }
    }

    /**
     * 设置订单状态（）
     * @param $order_id
     * @return bool
     */
    public function setOrderState($order_id)
    {
        return  Order::where('id',$order_id)
            ->update([
                'paid_at' => Carbon::now(),
                'payment_method' => 'transfer'
            ]);
    }

    /**
     * 删除转账数据
     * @param $id
     * @return mixed
     */
    public function delTransfer($id)
    {
        return $this->transferRepository->deleteTransfer($id);
    }

    /**
     * 创建转账数据
     * @param $data
     * @return mixed
     */
    public function creatTransfer($data)
    {
       $bool = $this->transferRepository->creatTransfer($data);
       if($bool){
           return ['status'=>'success','message'=>'转账信息已录入'];
       }else{
           return ['status'=>'fail','message'=>'转账信息录入失败'];
       }
    }
}
