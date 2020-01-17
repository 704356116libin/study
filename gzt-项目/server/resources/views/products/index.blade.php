@extends('layouts.app')
@section('title', '商品列表')

@section('content')
<style type="text/css">
  .products-index-page {
  .products-list {
    padding: 0 15px;
    .product-item {
      padding: 0 5px;
      margin-bottom: 10px;
      .product-content {
        border: 1px solid #eee;
        .top {
          padding: 5px;
          img {
            width: 100%;
          }
          .price {
            margin-top: 5px;
            font-size: 20px;
            color: #ff0036;
            b {
              font-size: 14px;
            }
          }
          .title {
            margin-top: 10px;
            height: 32px;
            line-height: 12px;
            max-height: 32px;
            a {
              font-size: 12px;
              line-height: 14px;
              color: #333;
              text-decoration: none;
            }
          }
        }
        .bottom {
          font-size: 12px;
          display: flex;
          .sold_count span {
            color: #b57c5b;
            font-weight: bold;
          }
          .review_count span {
            color: #38b;
            font-weight: bold;
          }
          &>div {
            &:first-child {
              border-right: 1px solid #eee;
            }
            padding: 10px 5px;
            line-height: 12px;
            flex-grow: 1;
            border-top: 1px solid #eee;
          }
        }
      }
    }
  }
}
</style>
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-body">
    <div class="row products-list">
      @foreach($products as $product)
        <div class="col-3 product-item">
          <div class="product-content">
            <div class="top">
              <div class="price"><b>￥</b>{{ $product->price }}</div>
              <a href="{{ route('products.show', ['product' => $product->id]) }}">{{ $product->title }}</a>
            </div>
            <div class="bottom">
              <div class="sold_count">销量 <span>{{ $product->sold_count }}笔</span></div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>
</div>
</div>
@endsection