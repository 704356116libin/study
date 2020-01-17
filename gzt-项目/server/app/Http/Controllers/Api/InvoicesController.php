<?php
namespace App\Http\Controllers\Api;



use App\Tools\FunctionTool;
use App\Tools\InvoiceTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class InvoicesController extends Controller
{
    private $invoiceTool;

    public function __construct()
    {
        $this->invoiceTool =  InvoiceTool::getInvoiceTool();
    }


    /**
     * 获取所有开票记录
     * @param $request
     * @return array
     */
   public function getInvoices(Request $request)
   {
        return $this->invoiceTool->getInvoiceList($request);
   }



    /**
     * 保存发票抬头
     * @param $request
     * @return array
     */
   public function saveInvoiceTitle(Request $request)
   {
       return $this->invoiceTool->saveInvoiceTitle($request,$request->user_id);
   }

    /**
     * 保存开票信息
     * @param $request
     * @return array
     */
   public function saveInvoice(Request $request)
   {
       return $this->invoiceTool->saveUserInvoiceMsg($request);
   }

    /**
     * 获取可以开票的订单列表
     * @param $request
     * @return mixed
     */
   public function getOrderList(Request $request)
   {
        return $this->invoiceTool->getOrderList($request);
   }

    /**
     * 删除发票抬头文件
     * @param $request
     * @return array
     */
   public function delInvoiceTitle(Request $request)
   {
       return $this->invoiceTool->deleteUserInvoiceTitle($request->id);
   }

    /**
     * 设置用户发票默认抬头
     * @param $request
     * @return array
     */
   public function setDefaultTitle(Request $request)
   {
       return $this->invoiceTool->setDefaultInvoiceTitle($request->user_id,$request->title_id);
   }

    /**
     * 获取某个票务信息详情
     * @param $request
     */
   public function getInvoiceDetail(Request $request)
   {
        return $this->invoiceTool->getInvoiceDetail($request->invoice_id);
   }
    /**
     * 设置用户开票状态
     * @param $request
     * @return array
     */
   public function setInvoiceState(Request $request)
   {
        return $this->invoiceTool->updateUserInvoiceState($request->invoice_id,$request->state);
   }

    /**
     * 获取用户默认发票头
     * @param Request $request
     * @return mixed
     */
   public function getDefaultInvoiceTitle(Request $request)
   {
       return $this->invoiceTool->getDefaultInvoiceTitle($request);
   }

    /**
     * 获取用户所有发票头
     * @param Request $request
     * @return \Illuminate\Support\Collection|mixed
     */
   public function getAllInvoiceTitle(Request $request)
   {
       return $this->invoiceTool->getAllInvoiceTitle($request);
   }

   public function test(Request $request)
   {
       return FunctionTool::encrypt_id($request->company_id);
   }


}