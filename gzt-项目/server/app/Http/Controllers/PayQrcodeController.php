<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use QrCode;


class PayQrcodeController extends Controller
{

    public function index()
    {
        $order_no = Order::findAvailableNo();
        $qrcode = QrCode::size(300)
            ->margin(0)
            ->generate(route('order.show',['sku_id'=>1,'amount'=>10,'length'=>2,'user_id'=>'11','t'=>'qeqewqeqeqweqweqw','order_no' => "$order_no"]));
        return view('orders.qrcode', compact('qrcode','order_no'));
    }

}
