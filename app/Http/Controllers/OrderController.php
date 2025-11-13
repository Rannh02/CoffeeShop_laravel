<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Display the list of orders
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 8;

        // Get total orders count
        $totalOrders = Order::count();
        $totalPages = ceil($totalOrders / $perPage);

        // Fetch orders for the current page
        $orders = Order::orderBy('Order_date', 'desc')
                       ->skip(($page - 1) * $perPage)
                       ->take($perPage)
                       ->get();

        return view('AdminDashboard.Orders', compact('orders', 'totalPages', 'page'));
    }

    // Store a new order (API endpoint)
    public function store(Request $request)
{
    if ($request->isJson()) {
        try {
            $data = $request->all();

            if (empty($data['orders'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No orders found'
                ], 400);
            }

            $customerName = $data['customerName'];
            $orderType = $data['orderType'];
            $employee_id = session('cashier_id') ?? Auth::id();

            if (!$employee_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No employee logged in.'
                ], 401);
            }

            DB::beginTransaction();

            // ✅ Handle or create customer
            $customer = Customer::firstOrCreate(
                ['Name' => $customerName],
                ['Date_Time' => now()]
            );

            // ✅ Compute total
            $totalAmount = collect($data['orders'])->sum(function ($item) {
                $qty = $item['quantity'] ?? $item['qty'] ?? 1;
                return $item['price'] * $qty;
            });

            // ✅ Create order
            $order = Order::create([
                'Customer_id' => $customer->Customer_id,
                'Employee_id' => $employee_id,
                'Customers Name' => $customerName,
                'OrderDate' => now(),
                'TotalAmount' => $totalAmount,
                'OrderType' => $orderType
            ]);

            // ✅ Loop through order items
            foreach ($data['orders'] as $item) {
                $qty = $item['quantity'] ?? $item['qty'] ?? 1;
                $price = $item['price'];

                // Find product
                $product = Product::where('Product_name', $item['name'])->first();
                if (!$product) {
                    throw new \Exception("Product not found: " . $item['name']);
                }

                // Create order item
                OrderItem::create([
                    'Order_id' => $order->Order_id,
                    'Product_id' => $product->Product_id,
                    'Quantity' => $qty,
                    'Price_sale' => $price
                ]);

                // ✅ Deduct ingredients and log to inventory
                if ($product->ingredients) {
                foreach ($product->ingredients as $ingredient) {
                    $totalUsed = $ingredient->pivot->Quantity_used * $qty;

                    // Deduct from ingredient stock
                    if ($ingredient->StockQuantity < $totalUsed) {
                        throw new \Exception("Not enough {$ingredient->Ingredient_name} in stock!");
                    }

                    $ingredient->StockQuantity -= $totalUsed;
                    $ingredient->save();

                    // Log to inventory usage
                    DB::table('inventories')->insert([
                        'Product_id' => $product->Product_id,
                        'Ingredient_id' => $ingredient->Ingredient_id,
                        'QuantityUsed' => $totalUsed,
                        'RemainingStock' => $ingredient->StockQuantity,
                        'DateUsed' => now(),
                        'Remarks' => "Used for order #{$order->Order_id}",
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }

            // ✅ Payment record
            $paymentMethod = $data['paymentMethod'] ?? 'Cash';
            $amountPaid = $data['amountPaid'] ?? $totalAmount;
            $referenceCode = $data['referenceCode'] ?? null;

            Payment::create([
                'Order_id' => $order->Order_id,
                'Payment_method' => $paymentMethod,
                'Amount_Paid' => $amountPaid,
                'PaymentDate' => now(),
                'Reference_Code' => $referenceCode
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order and ingredients recorded successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    return response()->json([
        'status' => 'error',
        'message' => 'Invalid request format.'
    ], 400);
}

     public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string',
            'order_type' => 'required|string',
            'total' => 'required|numeric',
            'orders' => 'required|array',
        ]);

        $order = Order::create([
            'Customer_name' => $validated['customer_name'],
            'Order_type' => $validated['order_type'],
            'Total_amount' => $validated['total'],
        ]);

        foreach ($validated['orders'] as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

            $product = Product::where('Product_name', $item['name'])->first();
            if ($product && $product->ingredients) {
                foreach ($product->ingredients as $ingredient) {
                    $ingredient->Stock -= ($ingredient->pivot->Quantity_used * $item['quantity']);
                    $ingredient->save();
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Order placed successfully!']);
    }
    public function showPOS()
{
    $products = Product::all();
    return view('AdminDashboard.pos', compact('products'));
}

}