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
        // Check if request is JSON
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
                
                // Get employee_id from session/auth
                $employee_id = session('cashier_id') ?? Auth::id();
                
                if (!$employee_id) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'No employee logged in.'
                    ], 401);
                }

                DB::beginTransaction();

                // Handle customer
                $customer = Customer::where('Name', $customerName)->first();
                
                if (!$customer) {
                    $customer = Customer::create([
                        'Name' => $customerName,
                        'Date_Time' => now()
                    ]);
                }
                $customerId = $customer->Customer_id;

                // Compute total amount
                $totalAmount = 0;
                foreach ($data['orders'] as $item) {
                    $qty = $item['quantity'] ?? $item['qty'] ?? 1;
                    $totalAmount += ($item['price'] * $qty);
                }

                // Insert order
                $order = Order::create([
                    'Customer_id' => $customerId,
                    'Employee_id' => $employee_id,
                    'Customers Name' => $customerName,
                    'OrderDate' => now(),
                    'TotalAmount' => $totalAmount,
                    'OrderType' => $orderType
                ]);

                $orderId = $order->Order_id;

                // Insert each product into orderitem
                foreach ($data['orders'] as $item) {
                    $qty = $item['quantity'] ?? $item['qty'] ?? 1;
                    $price = $item['price'];

                    // Determine product_id
                    if (isset($item['product_id'])) {
                        $productId = $item['product_id'];
                    } elseif (isset($item['name'])) {
                        $product = Product::where('Product_name', $item['name'])->first();
                        if (!$product) {
                            throw new \Exception("Product not found: " . $item['name']);
                        }
                        $productId = $product->Product_id;
                    } else {
                        throw new \Exception("Product information missing in order item.");
                    }

                    // Create order item
                    OrderItem::create([
                        'Order_id' => $orderId,
                        'Product_id' => $productId,
                        'Quantity' => $qty,
                        'Price_sale' => $price
                    ]);

                    // Deduct stock from inventory
                    $inventory = Inventory::where('Product_id', $productId)->first();
                    
                    if (!$inventory) {
                        throw new \Exception("No inventory record found for Product ID: $productId");
                    }

                    if ($inventory->QuantityInStock < $qty) {
                        throw new \Exception("Not enough stock for Product ID: $productId (Available: {$inventory->QuantityInStock}, Needed: $qty)");
                    }

                    $inventory->QuantityInStock -= $qty;
                    $inventory->save();
                }

                // Insert payment record
                $paymentMethod = $data['paymentMethod'] ?? 'Cash';
                $amountPaid = $data['amountPaid'] ?? $totalAmount;
                $referenceCode = $data['referenceCode'] ?? null;

                Payment::create([
                    'Order_id' => $orderId,
                    'Payment_method' => $paymentMethod,
                    'Amount_Paid' => $amountPaid,
                    'PaymentDate' => now(),
                    'Reference_Code' => $referenceCode
                ]);

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Order, items, and payment recorded successfully'
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
            'message' => 'Invalid request'
        ], 400);
    }
}