<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\PaymentController;

use App\Tools\OrderTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use QrCode;
class PayController extends Controller
{
    private $orderTool;
    private $orderCon;
    public function __construct(OrderTool $OrderTool)
    {
        $this->orderCon = new OrdersController();
        $this->orderTool = $OrderTool;
    }
    public function index()
    {
        $qrcode = QrCode::size(300)
            ->margin(0)
            ->generate(route('qrcode.index'));
        return view('orders.qrcode', compact('user', 'qrcode'));
    }

    /**
     * 获取抵扣金额
     */
    public function getSurplusDaysDeductedAmount()
    {
        return $body = [
            'code' => '1',
            'count' => '0',
            'vipLevel' => '1',

        ];

    }

    /**
     * 取消订单
     */
    public function cancelMemberOrder()
    {
        return $body = [
            'code' => '1',

        ];
    }
    public function getMemberPayUrl(PaymentController $pay, OrderRequest $request)
    {

        $user = User::find(11);
        $company = Company::find(1);
        $result = '';
        $orderStr = '';
        $pay_order = Order::where('no',$request->order_no)->first();
        if (is_null($pay_order)){
            $pay_order = $this->orderCon->store($request);
        }else{
            if ($pay_order->paid_at || $pay_order->closed){
                return $data = [
                    'code' => 3,
                    'messgae' => '该订单已经支付或者关闭',
                ];
            }
        }
        //weixin
        if ($request->pt == 2) {
            $result = $pay->payByWechat($pay_order, $request);
        } elseif ($request->pt == 1) {
            $orderStr = $pay->payByAlipay($pay_order, $request);
        } elseif($request->pt == 3) {
            $pay->transferPay($pay_order, $request);
//            return response('请使用支付宝或者微信扫码支付');
        }
        return $data = [
            'code' => 1,
            'orderStr' => $orderStr,
            'order_no' => $pay_order->no,
            'jsApiParams' => $result,
        ];
    }

}
