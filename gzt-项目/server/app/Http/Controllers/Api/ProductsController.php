<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Exceptions\InvalidRequestException;
class ProductsController extends Controller
{
    /**
     * @param Request $request
     * 获取全部产品
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getProducts(Request $request)
    {
        return Product::query()
            ->with(['skus'])
            ->where('on_sale', true)->get();
    }

    /**
     * @param Product $product
     * @param Request $request productId
     * @return Product|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     * @throws InvalidRequestException
     */
    public function getProduct(Product $product, Request $request)
    {
        $product = Product::query()
            ->with(['skus'])
            ->where('id', $request->product_id)->first();
        // 判断商品是否已经上架，如果没有上架则抛出异常。
        if (is_null($product)) {
            throw new InvalidRequestException('商品不存在');
        }
        if (!$product->on_sale) {
            throw new InvalidRequestException('商品未上架');
        }
        return $product;
    }
}
