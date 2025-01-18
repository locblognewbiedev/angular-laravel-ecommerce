<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $products = $this->productService->getAllProduct();
        return response()->json([
            'products' => $products,
        ], 200);
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'sold' => 'min:0',
            'amount' => 'min:0',
            'description' => 'nullable|string',
            'image' => 'required|string'
        ]);

        // Bắt đầu transaction
        DB::beginTransaction();

        try {
            // Tạo sản phẩm trong service
            $product = $this->productService->createProduct($validatedData);

            // Commit transaction nếu không có lỗi
            DB::commit();

            return response()->json([
                'message' => 'Product created successfully!',
                'product' => $product,
            ], 201);

        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            DB::rollBack();

            // Xử lý lỗi và trả về thông báo
            return response()->json([
                'message' => 'Product creation failed!',
                //'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function show($id)
    {
        $product = $this->productService->getProductById($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found.',
            ], 404);
        }

        return response()->json([
            'product' => $product,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'sold' => 'min:0',
            'amount' => 'min:0',
            'description' => 'nullable|string',
            'image' => 'required|string'
        ]);

        $updatedProduct = $this->productService->updateProduct($id, $validatedData);

        if (!$updatedProduct) {
            return response()->json([
                'message' => 'Product not found or update failed.',
            ], 404);
        }

        return response()->json([
            'message' => 'Product updated successfully!',
            'product' => $updatedProduct,
        ], 200);
    }

    public function destroy($id)
    {
        // $deleted = $this->productService->deleteProduct($id);

        // if (!$deleted) {
        //     return response()->json([
        //         'message' => 'Product not found or delete failed.',
        //     ], 404);
        // }

        // return response()->json([
        //     'message' => 'Product deleted successfully!',
        // ], 200);
    }
}
