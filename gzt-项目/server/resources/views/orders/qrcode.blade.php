@extends('layouts.app')
@section('title', '操作成功')

@section('content')
    <div class="card">
        <div class="card-header">二维码</div>
        <div class="card-body text-center">
            {!! $qrcode !!}
        </div>
        <div class="card-body text-center">
            <button class="text-center" onclick="getOrderNo()">测试获取orderno</button>
        </div>
        <input id="order_no" type="hidden" value="{{$order_no}}">
    </div>

@endsection
@section('scriptsAfterJs')
    <script>
        var data = {
            order_no: $('#order_no').val(),
        }
        var t1 = window.setInterval(getPaidByOrderNo, 5000);

        function getPaidByOrderNo() {
            axios.post('/api/getPaidByOrderNo', data
            ).then(function (res) {
                if (res.data.code == 1) {
                    alert('支付成功');
                    window.clearInterval(t1);
                }
                console.log(res);
            });
        }

        function getOrderNo() {
            axios.get('/api/getOrderNo'
            ).then(function (res) {
                console.log(res);
                data.order_no = res;
            });
        }

    </script>
@endsection