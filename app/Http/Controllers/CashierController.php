<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CashierController extends Controller
{
    /**
     * Show cashier login form
     */
    public function showLoginForm()
    {
        return view('LoginSystem.CashierLogin');
    }

    /**
     * Handle cashier login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required'
        ]);

        $employee = DB::table('employee')
            ->where('Cashier_Account', $request->username)
            ->first();

        Log::info('Cashier login attempt', ['username' => $request->username, 'found' => $employee ? true : false]);

        if ($employee && isset($employee->Password) && Hash::check($request->password, $employee->Password)) {
            $employeeId = $employee->Employee_id ?? ($employee->employee_id ?? null);
            Session::put('cashier_id', $employeeId);
            Session::put('cashier_name', trim(($employee->First_name ?? '') . ' ' . ($employee->Last_name ?? '')));

            return redirect()->route('cashier.pos');
        }

        return back()->withErrors(['username' => 'Invalid credentials'])->withInput();
    }

    /**
     * ✅ NEW: Check if a product has sufficient ingredient stock
     */
    public function checkProductAvailability($productId, $requestedQuantity)
    {
        $product = DB::table('products')->where('Product_id', $productId)->first();
        
        if (!$product) {
            return [
                'available' => false,
                'message' => 'Product not found.',
                'stock_info' => []
            ];
        }

        // Get all ingredients needed for this product
        $productIngredients = DB::table('product_ingredients')
            ->where('Product_id', $productId)
            ->get();

        if ($productIngredients->isEmpty()) {
            return [
                'available' => false,
                'message' => "{$product->Product_name} has no ingredients configured.",
                'stock_info' => []
            ];
        }

        $stockInfo = [];
        $insufficientIngredients = [];

        // Check each ingredient's stock
        foreach ($productIngredients as $productIngredient) {
            $ingredient = DB::table('ingredients')
                ->where('Ingredient_id', $productIngredient->Ingredient_id)
                ->first();

            if (!$ingredient) {
                return [
                    'available' => false,
                    'message' => "Ingredient ID {$productIngredient->Ingredient_id} not found.",
                    'stock_info' => []
                ];
            }

            $quantityNeeded = $productIngredient->Quantity_used * $requestedQuantity;
            $available = $ingredient->StockQuantity >= $quantityNeeded;
            
            $stockInfo[] = [
                'ingredient_name' => $ingredient->Ingredient_name,
                'needed' => $quantityNeeded,
                'available' => $ingredient->StockQuantity,
                'unit' => $ingredient->Unit,
                'sufficient' => $available
            ];

            // If insufficient, add to list
            if (!$available) {
                $insufficientIngredients[] = "{$ingredient->Ingredient_name} (Need: {$quantityNeeded} {$ingredient->Unit}, Available: {$ingredient->StockQuantity} {$ingredient->Unit})";
            }
        }

        if (!empty($insufficientIngredients)) {
            return [
                'available' => false,
                'message' => "Insufficient stock for: " . implode(', ', $insufficientIngredients),
                'stock_info' => $stockInfo
            ];
        }

        return [
            'available' => true,
            'message' => 'Product available',
            'stock_info' => $stockInfo
        ];
    }

    /**
     * ✅ NEW: API endpoint to check stock (for real-time checking)
     */
    public function checkStock($productId, $quantity)
    {
        $availability = $this->checkProductAvailability($productId, $quantity);
        return response()->json($availability);
    }

    /**
     * 🆕 MAIN DYNAMIC POS PAGE - Shows all categories with real stock checking
     */
    public function index(Request $request)
    {
        $staffName = "Cashier";

        if (Session::has('cashier_id')) {
            try {
                $employee = DB::table('employee')
                    ->select('First_name', 'Last_name')
                    ->where('Employee_id', Session::get('cashier_id'))
                    ->first();

                if ($employee) {
                    $staffName = $employee->First_name . " " . $employee->Last_name;
                }
            } catch (\Exception $e) {
                Log::error('Error fetching employee: ' . $e->getMessage());
                $staffName = "Error";
            }
        }

        $categorySlug = $request->get('category', null);
        $categories = collect([]);
        $products = collect([]);
        $selectedCategory = null;
        
        try {
            $categories = DB::table('categories')
                ->select('Category_id', 'Category_name')
                ->orderBy('Category_id')
                ->get();
            
            Log::info('Categories fetched: ' . $categories->count());
        } catch (\Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());
        }

        if ($categories->isNotEmpty()) {
            if ($categorySlug) {
                $selectedCategory = $categories->firstWhere(function($cat) use ($categorySlug) {
                    return strtolower(str_replace(' ', '-', $cat->Category_name)) === strtolower($categorySlug);
                });
            }
            
            if (!$selectedCategory) {
                $selectedCategory = $categories->first();
                $categorySlug = strtolower(str_replace(' ', '-', $selectedCategory->Category_name));
            }

            if ($selectedCategory) {
                try {
                    // Fetch products
                    $rawProducts = DB::table('products')
                        ->where('Category_id', $selectedCategory->Category_id)
                        ->get();

                    // ✅ ENHANCED: Check actual availability for each product
                    $products = $rawProducts->map(function($product) {
                        $availability = $this->checkProductAvailability($product->Product_id, 1);
                        
                        $product->is_available = $availability['available'];
                        $product->availability_message = $availability['message'];
                        $product->stock_info = $availability['stock_info'];
                        
                        return $product;
                    });
                    
                    Log::info('Products fetched for category ' . $selectedCategory->Category_name . ': ' . $products->count());
                    
                } catch (\Exception $e) {
                    Log::error('Error fetching products: ' . $e->getMessage());
                    $products = collect([]);
                }
            }
        } else {
            Log::warning('No categories found in database');
            $categorySlug = '';
        }

        return view('Cashier.pos', compact('staffName', 'categories', 'products', 'selectedCategory', 'categorySlug'));
    }

    /**
     * ⚠️ LEGACY METHODS
     */
    public function showCoffee()
    {
        return redirect()->route('cashier.pos', ['category' => 'coffee']);
    }

    public function showTea()
    {
        return redirect()->route('cashier.pos', ['category' => 'tea']);
    }

    public function showColdDrinks()
    {
        return redirect()->route('cashier.pos', ['category' => 'cold-drinks']);
    }

    public function showPastries()
    {
        return redirect()->route('cashier.pos', ['category' => 'pastries']);
    }

    /**
     * ✅ UPDATED: Store order with UPFRONT stock validation
     */
    public function storeOrder(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $orderData = $request->validate([
                'customer_name' => 'required|string',
                'order_type' => 'required|string|in:Dine In,Take Out,Takeout',
                'total' => 'required|numeric|min:0',
                'orders' => 'required|array|min:1',
                'orders.*.name' => 'required|string',
                'orders.*.quantity' => 'required|integer|min:1',
                'orders.*.price' => 'required|numeric|min:0',
                'amount_paid' => 'required|numeric|min:0',
                'payment_date' => 'nullable|string',
                'payment_method' => 'nullable|string',
                'transaction_reference' => 'nullable|string',
            ]);

            $paymentMethod = $orderData['payment_method'] ?? 'Cash';
            $transactionReference = $orderData['transaction_reference'] ?? 'CASH-' . time();
            $paymentDate = isset($orderData['payment_date']) 
                ? date('Y-m-d H:i:s', strtotime($orderData['payment_date'])) 
                : now();

            $employeeId = Session::get('cashier_id');
            
            if (!$employeeId) {
                return response()->json(['success' => false, 'message' => 'Not logged in'], 401);
            }

            // ✅ CRITICAL: CHECK ALL PRODUCTS AVAILABILITY BEFORE CREATING ORDER
            Log::info("=== VALIDATING STOCK FOR ALL PRODUCTS ===");
            
            foreach ($orderData['orders'] as $item) {
                $product = DB::table('products')
                    ->where('Product_name', $item['name'])
                    ->first();
                
                if (!$product) {
                    throw new \Exception("Product not found: {$item['name']}");
                }

                // ✅ CHECK STOCK AVAILABILITY
                $availability = $this->checkProductAvailability($product->Product_id, $item['quantity']);
                
                if (!$availability['available']) {
                    Log::warning("Stock check failed for {$product->Product_name}: {$availability['message']}");
                    throw new \Exception("Cannot process order: {$availability['message']}");
                }
                
                Log::info("✓ Stock validated for {$product->Product_name} x{$item['quantity']}");
            }

            Log::info("=== ALL PRODUCTS VALIDATED - PROCEEDING WITH ORDER ===");

            // Create or find customer
            $customer = DB::table('customer')
                ->where('Customer_name', $orderData['customer_name'])
                ->first();

            if (!$customer) {
                $customerId = DB::table('customer')->insertGetId([
                    'Customer_name' => $orderData['customer_name'],
                    'Date/Time' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                $customerId = $customer->Customer_id;
            }

            $orderType = $orderData['order_type'];
            if (strtolower($orderType) === 'take out') {
                $orderType = 'Takeout';
            }
            
            // Create the order
            $orderId = DB::table('orders')->insertGetId([   
                'Customer_id' => $customerId,
                'Employee_id' => $employeeId,
                'Customer_name' => $orderData['customer_name'],
                'Order_date' => now(),   
                'TotalAmount' => $orderData['total'],
                'Order_Type' => $orderType,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info("Order created: ID={$orderId}");

            // Create payment record
            $paymentId = DB::table('payment')->insertGetId([
                'Order_id' => $orderId,
                'PaymentMethod' => $paymentMethod,
                'AmountPaid' => $orderData['amount_paid'],
                'PaymentDate' => $paymentDate,
                'TransactionReference' => $transactionReference,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info("Payment created: ID={$paymentId}");

            // Process each order item and deduct stock
            foreach ($orderData['orders'] as $item) {
                $product = DB::table('products')
                    ->where('Product_name', $item['name'])
                    ->first();

                // Insert order item
                DB::table('order_items')->insert([
                    'Order_id' => $orderId,
                    'Product_id' => $product->Product_id,
                    'Quantity' => $item['quantity'],
                    'UnitPrice' => $item['price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Get ingredients
                $ingredients = DB::table('product_ingredients')
                    ->where('Product_id', $product->Product_id)
                    ->get();

                if ($ingredients->isEmpty()) {
                    Log::warning("No ingredients for {$product->Product_name}");
                    continue;
                }

                // Deduct each ingredient
                foreach ($ingredients as $ingredient) {
                    $totalQuantityUsed = $ingredient->Quantity_used * $item['quantity'];
                    
                    $inventory = DB::table('inventories')
                        ->where('Product_id', $product->Product_id)
                        ->where('Ingredient_id', $ingredient->Ingredient_id)
                        ->first();
                    
                    if (!$inventory) {
                        throw new \Exception("No inventory record for {$product->Product_name}");
                    }
                    
                    // Update inventory
                    DB::table('inventories')
                        ->where('Product_id', $product->Product_id)
                        ->where('Ingredient_id', $ingredient->Ingredient_id)
                        ->update([
                            'QuantityUsed' => DB::raw("QuantityUsed + {$totalQuantityUsed}"),
                            'RemainingStock' => DB::raw("RemainingStock - {$totalQuantityUsed}"),
                            'Action' => 'deduct',
                            'DateUsed' => now(),
                            'updated_at' => now(),
                        ]);

                    // ✅ Update ingredients table - prevent negative stock
                    $currentIngredientStock = DB::table('ingredients')
                        ->where('Ingredient_id', $ingredient->Ingredient_id)
                        ->value('StockQuantity');
                    
                    $newStock = max(0, $currentIngredientStock - $totalQuantityUsed);
                    
                    DB::table('ingredients')
                        ->where('Ingredient_id', $ingredient->Ingredient_id)
                        ->update([
                            'StockQuantity' => $newStock,
                            'updated_at' => now()
                        ]);

                    Log::info("Deducted {$totalQuantityUsed} of ingredient {$ingredient->Ingredient_id}. New stock: {$newStock}");
                }
            }

            DB::commit();
            
            Log::info("Order {$orderId} completed successfully!");
            
            return response()->json([
                'success' => true, 
                'message' => 'Order placed successfully!',
                'order_id' => $orderId,
                'payment_id' => $paymentId
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false, 
                'message' => 'Invalid order data',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Session::forget('cashier_id');
        Session::forget('cashier_name');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('welcome')->with('success', 'Logged out successfully');
    }
}