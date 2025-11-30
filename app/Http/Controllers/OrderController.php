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
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display the list of orders
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 8;

        // Get total orders count
         $search = request('search');
        $perPage = 8;

        // Fetch orders with search and pagination
        $orders = Order::with(['customer', 'employee', 'orderItems', 'payment'])
                       ->when($search, function($query, $search) {
                           $query->where('Customer_name', 'like', "%$search%")
                                 ->orWhere('Order_id', 'like', "%$search%")
                                 ->orWhere('Employee_id', 'like', "%$search%")
                                 ->orWhere('Order_Type', 'like', "%$search%")
                                 ->orWhere('TotalAmount', 'like', "%$search%");
                       })
                       ->orderBy('Order_date', 'desc')
                       ->paginate($perPage)
                       ->withQueryString(); // ✅ Preserves search query in pagination links

        return view('AdminDashboard.Orders', compact('orders'));

    }

    /**
     * Store a new order (handles both JSON API and form requests)
     */
    public function store(Request $request)
    {
        try {
            // Validate incoming data
            $validated = $request->validate([
                'customerName' => 'required|string|max:255',
                'orderType' => 'required|string|in:Dine In,Takeout',
                'orders' => 'required|array|min:1',
                'orders.*.name' => 'required|string',
                'orders.*.price' => 'required|numeric|min:0',
                'orders.*.quantity' => 'nullable|integer|min:1',
                'orders.*.qty' => 'nullable|integer|min:1',
                'paymentMethod' => 'nullable|string|in:Cash,GCash,Card',
                'amountPaid' => 'nullable|numeric|min:0',
                'referenceCode' => 'nullable|string|max:100'
            ]);

            // Get employee ID from session or auth
            $employee_id = session('cashier_id') ?? Auth::id();

            if (!$employee_id) {
                return $this->jsonResponse(false, 'No employee logged in.', null, 401);
            }

            DB::beginTransaction();

            // Create or find customer
            $customer = Customer::firstOrCreate(
                ['Name' => $validated['customerName']],
                ['Date_Time' => now()]
            );

            // Calculate total amount
            $totalAmount = collect($validated['orders'])->sum(function ($item) {
                $qty = $item['quantity'] ?? $item['qty'] ?? 1;
                return $item['price'] * $qty;
            });

            // Create order record
            $order = Order::create([
                'Customer_id' => $customer->Customer_id,
                'Employee_id' => $employee_id,
                'Customer_name' => $validated['customerName'],
                'Order_date' => now(),
                'TotalAmount' => $totalAmount,
                'Order_Type' => $validated['orderType']
            ]);

            // Process each order item
            foreach ($validated['orders'] as $item) {
                $qty = $item['quantity'] ?? $item['qty'] ?? 1;
                $price = $item['price'];

                // Find product
                $product = Product::where('Product_name', $item['name'])->first();
                
                if (!$product) {
                    throw new \Exception("Product '{$item['name']}' not found in inventory.");
                }

                // Create order item record
                OrderItem::create([
                    'Order_id' => $order->Order_id,
                    'Product_id' => $product->Product_id,
                    'Quantity' => $qty,
                    'Price_sale' => $price
                ]);

                // Deduct ingredients from stock
                if ($product->ingredients && $product->ingredients->count() > 0) {
                    foreach ($product->ingredients as $ingredient) {
                        $quantityUsedPerUnit = $ingredient->pivot->Quantity_used ?? 0;
                        $totalUsed = $quantityUsedPerUnit * $qty;

                        // Check if enough stock available
                        if ($ingredient->StockQuantity < $totalUsed) {
                            throw new \Exception(
                                "Insufficient stock for '{$ingredient->Ingredient_name}'. " .
                                "Required: {$totalUsed}, Available: {$ingredient->StockQuantity}"
                            );
                        }

                        // Deduct from ingredient stock
                        $ingredient->StockQuantity -= $totalUsed;
                        $ingredient->save();

                        // Log inventory usage
                        DB::table('inventories')->insert([
                            'Product_id' => $product->Product_id,
                            'Ingredient_id' => $ingredient->Ingredient_id,
                            'QuantityUsed' => $totalUsed,
                            'RemainingStock' => $ingredient->StockQuantity,
                            'DateUsed' => now(),
                            'Remarks' => "Order #{$order->Order_id} - {$validated['orderType']}",
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }

            // Create payment record
            $paymentMethod = $validated['paymentMethod'] ?? 'Cash';
            $amountPaid = $validated['amountPaid'] ?? $totalAmount;
            $referenceCode = $validated['referenceCode'] ?? null;

            Payment::create([
                'Order_id' => $order->Order_id,
                'Payment_method' => $paymentMethod,
                'Amount_Paid' => $amountPaid,
                'PaymentDate' => now(),
                'Reference_Code' => $referenceCode
            ]);

            DB::commit();

            // Log successful order
            Log::info('Order created successfully', [
                'order_id' => $order->Order_id,
                'customer' => $validated['customerName'],
                'total' => $totalAmount
            ]);

            // Return appropriate response
            $responseData = [
                'order_id' => $order->Order_id,
                'total_amount' => $totalAmount,
                'change' => $amountPaid - $totalAmount,
                'customer_name' => $validated['customerName'],
                'order_type' => $validated['orderType']
            ];

            if ($request->expectsJson()) {
                return $this->jsonResponse(true, 'Order placed successfully!', $responseData, 201);
            }

            return redirect()->route('orders.index')
                           ->with('success', 'Order placed successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            
            Log::warning('Order validation failed', ['errors' => $e->errors()]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return $this->jsonResponse(false, $e->getMessage(), null, 500);
            }
            
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Display POS interface
     */
    public function showPOS()
    {
        $products = Product::with('ingredients')->get();
        $customers = Customer::orderBy('Name')->get();
        
        return view('AdminDashboard.pos', compact('products', 'customers'));
    }

    /**
     * Get order details by ID
     */
    public function show($id)
    {
        try {
            $order = Order::with(['customer', 'employee', 'orderItems.product', 'payment'])
                         ->findOrFail($id);
            
            return view('AdminDashboard.OrderDetails', compact('order'));
        } catch (\Exception $e) {
            return back()->with('error', 'Order not found.');
        }
    }

    /**
     * Cancel an order
     */
    public function cancel($id)
    {
        try {
            DB::beginTransaction();

            $order = Order::with('orderItems.product.ingredients')->findOrFail($id);

            // Restore ingredients to stock
            foreach ($order->orderItems as $orderItem) {
                $product = $orderItem->product;
                
                if ($product && $product->ingredients) {
                    foreach ($product->ingredients as $ingredient) {
                        $quantityUsedPerUnit = $ingredient->pivot->Quantity_used ?? 0;
                        $totalUsed = $quantityUsedPerUnit * $orderItem->Quantity;

                        // Restore stock
                        $ingredient->StockQuantity += $totalUsed;
                        $ingredient->save();

                        // Log restoration
                        DB::table('inventories')->insert([
                            'Product_id' => $product->Product_id,
                            'Ingredient_id' => $ingredient->Ingredient_id,
                            'QuantityUsed' => -$totalUsed, // Negative to show restoration
                            'RemainingStock' => $ingredient->StockQuantity,
                            'DateUsed' => now(),
                            'Remarks' => "Order #{$order->Order_id} cancelled - stock restored",
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }

            // Delete order (cascade will handle order items and payment)
            $order->delete();

            DB::commit();

            return back()->with('success', 'Order cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order cancellation failed', ['error' => $e->getMessage()]);
            
            return back()->with('error', 'Failed to cancel order: ' . $e->getMessage());
        }
    }

    /**
     * Helper method for JSON responses
     */
    private function jsonResponse($success, $message, $data = null, $statusCode = 200)
    {
        $response = [
            'status' => $success ? 'success' : 'error',
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }
}