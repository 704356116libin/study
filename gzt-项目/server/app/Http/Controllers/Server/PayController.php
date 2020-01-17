<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Api\OrdersController;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use App\Tools\FunctionTool;
use App\Tools\SocialiteTool;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;

//use Jenssegers\Agent\Facades\Agent;
use Jenssegers\Agent\Agent;
use App\Http\Controllers\PaymentController;
use App\Tools\OrderTool;


/**
 * Class PayController
 * @package App\Http\Controllers\Server
 */
class PayController extends Controller
{
    /**
     * @var OrderTool
     */
    private $orderTool;
    /**
     * @var SocialiteTool
     */
    private $tool;
    /**
     * @var OrdersController
     */
    private $order;

    /**
     * PayController constructor.
     * @param OrderTool $OrderTool
     * @param SocialiteTool $tool
     * @param OrdersController $order
     */
    public function __construct(OrderTool $OrderTool, SocialiteTool $tool, OrdersController $order)
    {
        $this->orderTool = $OrderTool;
        $this->tool = $tool;
        $this->order = $order;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $code = $request->code;

        if (!is_null($code)) {
            $resultdata = $this->tool->getWeChatAccessToken($code);
            $this->resultdata = $resultdata;
            $openid = $resultdata['openid'];
            Session()->put('openid', $openid);
        }
        return view('orders.show');
    }

    /**
     * @param PaymentController $pay
     * @param OrderRequest $request
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \App\Exceptions\InvalidRequestException
     */
    public function getMemberPayUrl(PaymentController $pay, OrderRequest $request)
    {

        $user = User::find(1);
        $company = Company::find(1);
        $result = '';
        $orderStr = '';
        if(Order::query()->where('no', $request->order_no)->exists()){
            $pay_order = Order::where('no',$request->order_no)->first();
            if ($pay_order->paid_at || $pay_order->closed){
                return $data = [
                    'code' => 3,
                    'messgae' => '该订单已经支付或者关闭',
                ];
            }
        }else{
            $pay_order = $this->order->store($request);
        }
        //weixin
        if ($request->pt == 2) {
            $result = $pay->payByWechat($pay_order, $request);
        } elseif ($request->pt == 1) {
            $orderStr = $pay->payByAlipay($pay_order, $request);
        } else {
            return response('请使用支付宝或者微信扫码支付');
        }
        return $data = [
            'code' => 1,
            'orderStr' => $orderStr,
            'order_no' => "$pay_order->no",
            'jsApiParams' => $result,
        ];
    }

    /**
     * @param PaymentController $pay
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \App\Exceptions\InvalidRequestException
     */
    public function pay(PaymentController $pay, Request $request)
    {
        $items[0]['sku_id'] = $request->sku_id;
        $items[0]['amount'] = $request->amount;
        $items[0]['length'] = $request->length;
        $remark = $request->remark ? '' : '';
        // $user = User::find(FunctionTool::decrypt_id($request->user_id));
        $user = User::find(1);
        $company = Company::find($user->current_company_id);

        $agent = new Agent();
        if (!$agent->isMobile()) {
            return response('PC 访问');
        }

        if ($agent->isMobile()) {
            // 判断是微信
            if (strpos($agent->getUserAgent(), 'MicroMessenger')) {
//                dd('微信');
                $pay_order = $this->orderTool->store($user, $company, $remark, $items);

                return $pay->payByWechat($pay_order, $request);
            } // 判断是支付宝
            elseif (strpos($agent->getUserAgent(), 'AlipayClient')) {
//                dd('支付宝');
                $pay_order = $this->orderTool->store($user, $company, $remark, $items);

                return $pay->payByAlipay($pay_order, $request);

//                return response($pay_out);
            } else {
                return response('请使用支付宝或者微信扫码支付');
            }

        }

    }
}
