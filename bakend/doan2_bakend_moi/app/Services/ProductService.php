<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    function getAllProduct()
    {
        $products = Product::all();
        return $products;
    }

    function createProduct($data)
    {
        if (isset($data['image']) && $data['image']) {
            $data['image'] = UploadImageService::uploadImage($data['image']);
        }
        return Product::create($data);
        //TModel là gì?
    }
    function getProductById($id)
    {
        return Product::findOrFail($id);

    }
    public function updateProduct(int $id, array $data)
    {
        // Tìm sản phẩm theo ID
        $product = Product::find($id);

        if (!$product) {
            // Nếu không tìm thấy sản phẩm, trả về null
            return null;
        }

        // Cập nhật các trường của sản phẩm
        $product->update($data);

        // Trả về sản phẩm đã được cập nhật
        return $product;
    }

}