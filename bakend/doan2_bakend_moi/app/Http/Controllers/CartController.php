<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Lấy user ID thông qua Sanctum
        $user = $request->user(); // User đang xác thực qua Sanctum
        $userId = $user->id;

        // Lấy dữ liệu từ request
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id', // Kiểm tra product_id có tồn tại
            'number_product' => 'required|integer|min:1', // Kiểm tra số lượng là số nguyên và >= 1
        ]);

        $productId = $validatedData['product_id'];
        $numberProduct = $validatedData['number_product'];

        // Kiểm tra xem sản phẩm đã có trong giỏ hàng của người dùng chưa
        $cart = Cart::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($cart) {
            // Nếu đã tồn tại, tăng số lượng sản phẩm
            $cart->number_product += $numberProduct;
            $cart->save();
        } else {
            // Nếu chưa tồn tại, tạo mới
            $cart = Cart::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'number_product' => $numberProduct,
            ]);
        }

        // Trả về phản hồi JSON
        return response()->json([
            'message' => 'Product added to cart successfully!',
            'cart' => $cart,
        ], 201);
    }

    public function showCart(Request $request)
    {
        $user = $request->user(); // Lấy user từ Sanctum

        // Lấy giỏ hàng của người dùng kèm thông tin sản phẩm
        $cartItems = Cart::with('product')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($cart) {
                return [
                    'cart_id' => $cart->id,
                    'number_product' => $cart->number_product,
                    'product' => [
                        'id' => $cart->product->id,
                        'name' => $cart->product->name,
                        'description' => $cart->product->description,
                        'price' => $cart->product->price,
                        'amount' => $cart->product->amount,
                        'image' => $cart->product->image,
                    ],
                ];
            });

        return response()->json([
            'message' => 'Cart retrieved successfully!',
            'cart' => $cartItems,
        ], 200);
    }




    /**
     * Display the specified resource.
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        //
    }
    public function deleteCartItem(Request $request)
    {
        $user = $request->user(); // Lấy thông tin user đang đăng nhập qua Sanctum

        // Xác thực dữ liệu đầu vào
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id', // Kiểm tra product_id có tồn tại
        ]);

        $productId = $validatedData['product_id'];

        // Tìm sản phẩm trong giỏ hàng
        $cartItem = Cart::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'message' => 'Cart item not found.',
            ], 404);
        }

        // Xóa sản phẩm khỏi giỏ hàng
        $cartItem->delete();

        return response()->json([
            'message' => 'Cart item deleted successfully!',
        ], 200);
    }

}
