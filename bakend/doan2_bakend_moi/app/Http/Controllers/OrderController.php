<?php

namespace App\Http\Controllers;
use App\Mail\OrderStatusUpdatedMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helpers;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::all();
        return response()->json(
            ["orders" => $orders]
        );
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
    // public function store(Request $request)
    // {
    //     // Lấy user từ Sanctum
    //     $user = $request->user();

    //     // Validate dữ liệu đầu vào
    //     $validatedData = $request->validate([
    //         'items' => 'required|array',
    //         'items.*.product_id' => 'required|exists:products,id',
    //         'items.*.quantity' => 'required|integer|min:1',
    //         'phone_number' => 'required|string|max:15',
    //         'address' => 'required|string|max:255',
    //     ]);

    //     // Sử dụng transaction
    //     return DB::transaction(function () use ($validatedData, $user) {
    //         $items = $validatedData['items'];

    //         // Tính tổng giá trị đơn hàng
    //         $orders = [];
    //         foreach ($items as $item) {
    //             $product = Product::findOrFail($item['product_id']);
    //             $orders[] = [
    //                 'price' => $product->price,
    //                 'quantity' => $item['quantity'],
    //             ];
    //         }

    //         $totalPrice = Helpers::calcTotalPrice($orders);

    //         // Tạo order
    //         $order = Order::create([
    //             'user_id' => $user->id,
    //             'total_price' => $totalPrice,
    //             'phone_number' => $validatedData['phone_number'],
    //             'address' => $validatedData['address'],
    //         ]);

    //         // Lưu chi tiết đơn hàng
    //         foreach ($items as $item) {
    //             $order->orderDetails()->create([
    //                 'product_id' => $item['product_id'],
    //                 'quantity' => $item['quantity'],
    //                 'price' => Product::findOrFail($item['product_id'])->price,
    //             ]);
    //         }

    //         // Xóa giỏ hàng của người dùng
    //         Cart::where('user_id', $user->id)->delete();

    //         return response()->json([
    //             'message' => 'Order created successfully.',
    //             'order' => $order,
    //         ], 201);
    //     });
    // }
    public function store(Request $request)
    {
        // Lấy user từ Sanctum
        $user = $request->user();

        // Validate dữ liệu đầu vào
        $validatedData = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'phone_number' => 'required|string|max:15',
            'address' => 'required|string|max:255',
        ]);

        // Sử dụng transaction
        return DB::transaction(function () use ($validatedData, $user) {
            $items = $validatedData['items'];

            // Tính tổng giá trị đơn hàng
            $orders = [];
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Kiểm tra xem số lượng đặt hàng có vượt quá số lượng còn lại hay không
                if ($product->amount < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product ID {$item['product_id']}.");
                }

                $orders[] = [
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                ];
            }

            $totalPrice = Helpers::calcTotalPrice($orders);

            // Tạo order
            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => $totalPrice,
                'phone_number' => $validatedData['phone_number'],
                'address' => $validatedData['address'],
            ]);

            // Lưu chi tiết đơn hàng và cập nhật sản phẩm
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Tạo order detail
                $order->orderDetails()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);

                // Cập nhật cột sold và amount
                $product->increment('sold', $item['quantity']);
                $product->decrement('amount', $item['quantity']);
            }

            // Xóa giỏ hàng của người dùng
            Cart::where('user_id', $user->id)->delete();

            return response()->json([
                'message' => 'Order created successfully.',
                'order' => $order,
            ], 201);
        });
    }

    public function getOrderForUser(Request $request)
    {
        // Lấy thông tin người dùng từ Sanctum
        $user = $request->user();

        // Kiểm tra xem người dùng có tồn tại hay không
        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated.'
            ], 401);
        }

        // Lấy danh sách các đơn hàng của người dùng
        $orders = Order::where('user_id', $user->id)
            ->with('orderDetails.product') // Load chi tiết đơn hàng và thông tin sản phẩm
            ->get();

        // Kiểm tra xem người dùng có đơn hàng nào không
        if ($orders->isEmpty()) {
            return response()->json([
                'message' => 'No orders found for this user.'
            ], 404);
        }

        // Trả về danh sách đơn hàng
        return response()->json([
            'message' => 'Orders retrieved successfully.',
            'orders' => $orders,
        ], 200);
    }




    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Lấy thông tin đơn hàng dựa trên id và user đã đăng nhập


        // Tìm order theo id, đảm bảo order thuộc về user hiện tại
        $order = Order::where('id', $id)

            ->with('orderDetails.product') // Load chi tiết đơn hàng kèm thông tin sản phẩm
            ->first();
        // Kiểm tra nếu không tìm thấy order
        if (!$order) {
            return response()->json([
                'message' => 'Order not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Order details retrieved successfully.',
            'order' => $order,
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }

    public function updateStatusOrder(Request $request, $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'status' => 'required|string|in:pending,processing,completed,cancelled',
        ]);

        // Find the order by ID
        $order = Order::find($id);
        $useremail = $order->user->email;
        // $useremail = 'hoctienganhcungannanhu@gmail.com';
        // Check if the order exists
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Update the order's status
        $order->status = $validated['status'];
        $order->save();
        Mail::to($useremail)->send(new OrderStatusUpdatedMail($order));
        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order,
        ]);
    }

    public function getOrderStatsByMonthAndYear(Request $request)
    {
        // Lấy tháng và năm từ request (hoặc mặc định nếu không có)
        $month = $request->input('month');
        $year = $request->input('year');

        // Kiểm tra nếu tháng và năm hợp lệ
        if (!$month || !$year) {
            return response()->json(['error' => 'Tháng và năm không hợp lệ.'], 400);
        }

        // Truy vấn số lượng đơn hàng theo trạng thái "completed" và "cancelled" cho tháng và năm cụ thể
        $stats = DB::table('orders')
            ->selectRaw('
            SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_orders,
            SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_orders'
            )
            ->whereMonth('created_at', $month) // Lọc theo tháng
            ->whereYear('created_at', $year)   // Lọc theo năm
            ->first();  // Chỉ cần 1 bản ghi duy nhất vì chỉ lọc theo tháng và năm

        // Trả về kết quả
        return response()->json([
            'completed_orders' => $stats->completed_orders ?? 0,
            'cancelled_orders' => $stats->cancelled_orders ?? 0
        ]);
    }
    public function getRevenueByMonthAndYear(Request $request)
    {
        // Lấy tháng và năm từ request (hoặc mặc định nếu không có)
        $month = $request->input('month');
        $year = $request->input('year');

        // Kiểm tra nếu tháng và năm hợp lệ
        if (!$month || !$year) {
            return response()->json(['error' => 'Tháng và năm không hợp lệ.'], 400);
        }

        // Truy vấn tổng doanh thu cho trạng thái "completed" cho tháng và năm cụ thể
        $revenue = DB::table('orders')
            ->where('status', 'completed') // Lọc theo trạng thái 'completed'
            ->whereMonth('created_at', $month) // Lọc theo tháng
            ->whereYear('created_at', $year)   // Lọc theo năm
            ->sum('total_price');  // Tính tổng giá trị 'total_price' của các đơn hàng

        // Trả về kết quả doanh thu
        return response()->json([
            'revenue' => $revenue ?? 0
        ]);
    }
    public function getMonthlyRevenueByYear($year)
    {
        // Lấy năm từ request
        //$year = $request->input('year');

        // Kiểm tra nếu năm hợp lệ
        if (!$year) {
            return response()->json(['error' => 'Năm không hợp lệ.'], 400);
        }

        // Truy vấn doanh thu theo tháng cho năm cụ thể sử dụng strftime
        $monthlyRevenue = DB::table('orders')
            ->select(
                DB::raw("strftime('%m', created_at) as month"),
                DB::raw('SUM(total_price) as total_revenue')
            )
            ->where('status', 'completed') // Chỉ lấy đơn hàng trạng thái 'completed'
            ->whereYear('created_at', $year) // Lọc theo năm
            ->groupBy(DB::raw("strftime('%m', created_at)")) // Nhóm theo tháng
            ->orderBy('month', 'asc') // Sắp xếp theo tháng từ tháng 1 đến tháng 12
            ->get();

        // Khởi tạo mảng doanh thu cho 12 tháng, mặc định là 0
        $revenueByMonth = array_fill(0, 12, 0);

        // Điền doanh thu của các tháng có dữ liệu
        foreach ($monthlyRevenue as $data) {
            $revenueByMonth[(int) $data->month - 1] = $data->total_revenue;
        }

        // Trả về mảng doanh thu theo tháng
        return response()->json([
            'year' => $year,
            'monthly_revenue' => $revenueByMonth
        ]);
    }


}
