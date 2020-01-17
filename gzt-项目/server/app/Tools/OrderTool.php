<?php

namespace App\Tools;

use App\Models\User;
use App\Models\Company;
use App\Models\Order;
use App\Models\ProductSku;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use Carbon\Carbon;

class OrderTool
{
    public function store(User $user,Company $company, $remark, $items,$no)
    {
        // 开启一个数据库事务
        $order = \DB::transaction(function () use ($user, $company, $remark, $items,$no) {
            // 创建一个订单
            $order   = new Order([
                'no'       => $no,
                'remark'       => $remark,
                'total_amount' => 0,
                'original_amount' => 0,
                'discount_amount' => 0,
                'deduction_amount' => 0,
            ]);
            // 订单关联到当前用户
            $order->user()->associate($user);
            $order->company()->associate($company);
            // 写入数据库
            $order->save();

            $totalAmount = 0;
            $originalAmount = 0;
            $discountAmount = 0;
            $deductionAmount = 0;
            // 遍历用户提交的 SKU
            foreach ($items as $data) {
                $sku  = ProductSku::find($data['sku_id']);
                // 创建一个 OrderItem 并直接与当前订单关联
                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'length' => $data['length'],
                    'price'  => $sku->price,
                ]);
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                if ($data['length'] == 1 ){
                    $discount = 1;
                }elseif ($data['length'] == 2){
                    $discount = 0.9;
                }elseif ($data['length'] == 3){
                    $discount = 0.8;
                }else{
                    $discount = 0.8;
                }
                $originalAmount += $sku->price * $data['amount'];
                $discountAmount += $originalAmount * (1-$discount) ;
                $deduction = 1;
                if ($sku->product_id == 1){
                    $deduction = $this->deduction($company);
                }
                $deductionAmount +=  $originalAmount * (1-$deduction);

                $totalAmount += $originalAmount - $discountAmount - $deductionAmount;
                if ($totalAmount <= 0) {
                    throw new InvalidRequestException('该商品价格错误');
                }
            }
            // 更新订单总金额
            $order->update([
                'total_amount' => $totalAmount,
                'original_amount' => $originalAmount,
                'discount_amount' => $discountAmount,
                'deduction_amount' => $deductionAmount,
            ]);
            return $order;
        });

        // 这里我们直接使用 dispatch 函数
        dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
    }

    public function deduction($company)
    {
        $companyBasisLimit = $company->companyBasisLimit()->where('type', 'staff_number')->first();
        if (!empty($companyBasisLimit)){
            if ($companyBasisLimit->type_number > 10 && Carbon::parse($companyBasisLimit->expire_date) > Carbon::now()){
                $int = Carbon::now()->diffInDays(Carbon::parse($companyBasisLimit->expire_date),true);
                return $deduction = $int/365;
            }
        }
        return $deduction = 1;
    }


}