<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\OrderItem;
use App\Models\Company;
use App\Exceptions\InvalidRequestException;
use Carbon\Carbon;
Use Illuminate\Support\Facades\Log;

class GiveProduct
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * 购买后给予对应商品或者权限
     * @param  OrderPaid $event
     * @return void
     */
    public function handle(OrderPaid $event)
    {
        // 从事件对象中取出对应的订单
        $order = $event->getOrder();
        // 预加载商品数据
        $order->load('items.product');
        // $order->load('company.sms_count');
        // 循环遍历订单的商品
        foreach ($order->items as $item) {
            $company = $order->company;
            $productTitle = $item->product->title;
            $companyBasic = $order->company->companyBasisLimit()->where('type', 'staff_number')->first();
            $companyBasicSms = $order->company->companyBasisLimit()->where('type', 'sms')->first();
            if ($productTitle == '组织人数') {
                if ($order->deduction_amount > 0){
                    $companyBasic->update([
                        'type_number' => $item->amount + $companyBasic->type_number > 10 ? $companyBasic->type_number : 0,
                    ]);
                }else{
                    $companyBasic->update([
                        'type_number' => $item->amount + $companyBasic->type_number > 10 ? $companyBasic->type_number : 0,
                        'expire_date' =>strtotime( Carbon::now()->addYears($item->length) ),
                    ]);
                }
            } elseif ($productTitle == '短信服务') {
                $companyBasicSms->update([
                    'type_number' => $item->amount + $companyBasicSms->type_number,
                    'expire_date' => strtotime(Carbon::now()->addYears($item->length)->timestamp),
                ]);
            } elseif ($productTitle == '网盘存储') {
                $company->oss->update([
                    'all_size' => $item->amount + $company->oss->all_size,
                ]);
            } else {
                throw new InvalidRequestException('无此商品，订单授权失败');
            }
            if ($order->deduction_amount > 0) {
                $item->update([
                    'expire_at' => Carbon::parse($companyBasic->expire_date),
                ]);
            } else {
                $item->update([
                    'expire_at' => Carbon::now()->addYears($item->length),
                ]);
            }
        }
    }
}
