<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Models\Product;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);

Route::apiResource('/products', ProductController::class);

Route::post('carts/add', [CartController::class, 'store'])->middleware('auth:sanctum');
Route::get('carts/show', [CartController::class, 'showCart'])->middleware('auth:sanctum');
Route::delete('carts/delete', [CartController::class, 'deleteCartItem'])->middleware('auth:sanctum');
Route::get('featureProducts', function () {
    $topProducts = Product::orderBy('sold', 'desc')
        ->take(6)
        ->get();
    return response()->json($topProducts);
});

Route::get('newProducts', function () {
    $newProducts = Product::orderBy('created_at', 'desc')
        ->take(6)
        ->get();
    return response()->json($newProducts);
});
Route::post('orders/add', [OrderController::class, 'store'])->middleware('auth:sanctum');
Route::get('orders', [OrderController::class, 'index'])->middleware('auth:sanctum');
Route::get('orders/detail/{id}', [OrderController::class, 'show'])->middleware('auth:sanctum');

Route::get('getOrderForUser', [OrderController::class, 'getOrderForUser'])->middleware('auth:sanctum');
Route::put('/orders/{id}/status', [OrderController::class, 'updateStatusOrder']);

Route::get('orders/getOrderStatsByMonthAndYear', [OrderController::class, 'getOrderStatsByMonthAndYear']);
Route::get('/revenue', [OrderController::class, 'getRevenueByMonthAndYear']);
Route::get('/revenue-by-year/{year}', [OrderController::class, 'getMonthlyRevenueByYear']);

Route::post('setLogo', [SettingController::class, 'setLogo']);
Route::get('getLogo', [SettingController::class, 'getLogo']);

Route::get('getAD', [SettingController::class, 'getImageAD']);
Route::post('setAD', [SettingController::class, 'setImageAD']);