<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\Company;
use App\Models\User;
use App\Models\Order;
use App\Tools\OrderTool;

class OrdersController extends Controller
{
    /**
     * OrdersController constructor.
     */
    private $orderTool;

    public function __construct(OrderTool $OrderTool)
    {
        $this->orderTool = $OrderTool;
    }

    /**
     * @param Request $request
     * 获取某个公司订单
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getOrders(Request $request)
    {
        $orders = Order::query()
            // 使用 with 方法预加载，避免N + 1问题
            ->with(['items.product', 'items.productSku'])
            ->where('user_id', $request->user()->id)
            ->where('company_id', $request->company_id)
            ->orderBy('created_at', 'desc')
            ->get();
        return $orders;
    }

    /**
     * @param Request $request
     * 获取某个订单详情
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getOrder(Request $request)
    {
        $this->authorize('own', Order::find($request->order_id));
        return $this->getOrders($request)->where('id', $request->order_id);
    }


    public function getPaidByOrderNo(Request $request)
    {

        $order = Order::where('no', $request->order_no)->first();
        if (is_null($order)) {
            $data = [
                'code' => 0,
                'message' => '订单不存在',
            ];
        } else {

            if ($order->paid_at) {
                $data = [
                    'code' => 1,
                    'message' => '订单已支付',
                ];
            } else {
                if ($order->closed) {
                    $data = [
                        'code' => 2,
                        'message' => '订单未支付，已关闭',
                    ];
                } else {
                    $data = [
                        'code' => 3,
                        'message' => '订单未支付，未关闭',
                    ];
                }
            }
        }
        return $data;

    }

    public function getOrderNo()
    {
       return $orderNo = Order::findAvailableNo();
    }

    /**
     * 存储订单
     * @param OrderRequest $request
     * @param OrderTool $OrderTool
     * @return mixed
     */
    public function store(Request $request)
    {
        return $this->orderTool->store(User::find(11), Company::find(1), $request->remark, $request->items,$request->order_no);
    }

    public function beforPay()
    {

    }
}
