@extends('layouts.app')
@section('title', $product->title)

@section('content')
<style type="text/css">
  .products-show-page {
   .cover {
     width: 100%;
     border: solid 1px #eee;
     padding: 30px 0;
   }
   .title {
     font-size: 24px;
     font-weight: bold;
     margin-bottom: 10px;
   }
   .price {
     label {
       width: 69px;
       color: #999;
       font-size: 12px;
       padding-left: 10px;
     }
     em {
       font-family: Arial;
       font-size: 18px;
       font-style: normal;
       text-decoration: none;
       vertical-align: middle;
     }
     span {
       font-family: Arial;
       font-size: 24px;
       font-weight: bolder;
       text-decoration: none;
       vertical-align: middle;
     }
     line-height: 30px;
     background-color: #e9e9e9;
     color: red;
     font-size: 20px;
   }
   .sales_and_reviews {
     border-top: 1px dotted #c9c9c9;
     border-bottom: 1px dotted #c9c9c9;
     margin: 5px 0 10px;
     display: flex;
     flex-direction: row;
     font-size: 12px;
     &>div {
       &.sold_count,&.review_count {
         border-right: 1px dotted #c9c9c9;
       }
       width: 33%;
       text-align: center;
       padding: 5px;
       .count {
         color: #FF0036;
         font-weight: 700;
       }
     }
   }
   .skus {
     &>label {
       color: #999;
       font-size: 12px;
       padding: 0 10px 0 10px;
     }
     .btn-group {
       margin-left: -10px;
       label {
         border-radius: 0 !important;
         margin: 1px 5px;
         padding: 2px 5px;
         font-size: 12px;
       }
       .btn {
         border: 1px solid #ccc;
       }
       .btn.active, .btn:hover {
         margin-top: 0px !important;
         background: #fff !important;
         border: 2px solid red !important;
       }
       .btn.focus {
         outline: 0 !important;
       }
     }
   }
   .cart_amount {
     label {
       color: #999;
       font-size: 12px;
       padding: 0 10px 0 10px;
     }
     font-size: 12px;
     color: #888;
     margin: 10px 0 20px;
     input {
       width: 50px;
       display: inline-block;
       border-radius: 0 !important;
     }
     span {
       color: #999;
       font-size: 12px;
       padding-left: 10px;
     }
   }
   .buttons {
     padding-left: 44px;
   }

   .product-detail  {
     .nav.nav-tabs > li > a {
       border-radius: 0 !important;
     }
     margin: 20px 0;
     .tab-content {
       border: 1px solid #eee;
       padding: 20px;
     }
   }
 }
</style>
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-body product-info">
    <div class="row">
      <div class="col-7">
        <div class="title">{{ $product->title }}</div>
        <div class="price"><label>价格</label><em>￥</em><span>{{ $product->price }}</span></div>
        <div class="sales_and_reviews">
          <div class="sold_count">累计销量 <span class="count">{{ $product->sold_count }}</span></div>
        </div>
        <div class="skus">
          <label>选择</label>
          <div class="btn-group btn-group-toggle" data-toggle="buttons">
            @foreach($product->skus as $sku)
  <label
      class="btn sku-btn"
      data-price="{{ $sku->price }}"
      data-toggle="tooltip"
      title="{{ $sku->description }}"
      data-placement="bottom">
    <input type="radio" name="skus" autocomplete="off" value="{{ $sku->id }}"> {{ $sku->title }}
  </label>
@endforeach
          </div>
        </div>
      </div>
    </div>
    <div class="product-detail">
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" href="#product-detail-tab" aria-controls="product-detail-tab" role="tab" data-toggle="tab" aria-selected="true">商品详情</a>
        </li>
      </ul>
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="product-detail-tab">
          {!! $product->description !!}
        </div>
        <div role="tabpanel" class="tab-pane" id="product-reviews-tab">
        </div>
      </div>
    </div>
  </div>
</div>
<form class="form-horizontal" role="form" id="order-form">
  <div class="form-group">
      <div class="offset-sm-3 col-sm-3">
        <button type="button" class="btn btn-primary btn-create-order">提交订单</button>
      </div>
    </div>
</form>
</div>
</div>
@endsection
@section('scriptsAfterJs')
<script>
  $(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});
    $('.sku-btn').click(function () {
      $('.product-info .price span').text($(this).data('price'));
    });


    // 监听创建订单按钮的点击事件
    $('.btn-create-order').click(function () {
      // 构建请求参数，将用户选择的地址的 id 和备注内容写入请求参数
      var req = {
        company:1,
        items: [],
        remark: '备注',
      };
  
        req.items.push({
          sku_id: 8,
          amount: 50,
        });

      axios.post('/api/orders', req)
        .then(function (response) {
          swal('订单提交成功', '', 'success');
        }, function (error) {
          if (error.response.status === 422) {
            // http 状态码为 422 代表用户输入校验失败
            var html = '<div>';
            _.each(error.response.data.errors, function (errors) {
              _.each(errors, function (error) {
                html += error+'<br>';
              })
            });
            html += '</div>';
            swal({content: $(html)[0], icon: 'error'})
          } else {
            // 其他情况应该是系统挂了
            swal('系统错误', '', 'error');
          }
        });
    });

  });
</script>
@endsection