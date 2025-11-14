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
        // validate username (cashier account) and password
        $request->validate([
            'username' => 'required|string',
            'password' => 'required'
        ]);

        // Attempt to find the employee by Cashier_Account
        $employee = DB::table('employee')
            ->where('Cashier_Account', $request->username)
            ->first();

        // Log for debugging (do NOT log sensitive data like passwords)
        Log::info('Cashier login attempt', ['username' => $request->username, 'found' => $employee ? true : false]);

        // Verify credentials
        if ($employee && isset($employee->Password) && Hash::check($request->password, $employee->Password)) {
            // store Employee primary id (Employee_id)
            $employeeId = $employee->Employee_id ?? ($employee->employee_id ?? null);
            Session::put('cashier_id', $employeeId);
            Session::put('cashier_name', trim(($employee->First_name ?? '') . ' ' . ($employee->Last_name ?? '')));

            return redirect()->route('cashier.pos');
        }

        return back()->withErrors(['username' => 'Invalid credentials'])->withInput();
    }

    /**
     * 🆕 MAIN DYNAMIC POS PAGE - Shows all categories
     */
    public function index(Request $request)
{
    // Default staff name
    $staffName = "Cashier";

    // If logged in, fetch cashier info
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

    // Get the category from URL parameter
    $categorySlug = $request->get('category', null);
    
    // Initialize with empty collections
    $categories = collect([]);
    $products = collect([]);
    $selectedCategory = null;
    
    // Fetch all categories for navigation
    try {
        $categories = DB::table('categories')
            ->select('Category_id', 'Category_name')
            ->orderBy('Category_id')
            ->get();
        
        Log::info('Categories fetched: ' . $categories->count());
    } catch (\Exception $e) {
        Log::error('Error fetching categories: ' . $e->getMessage());
    }

    // Only proceed if we have categories
    if ($categories->isNotEmpty()) {
        // Find the selected category
        if ($categorySlug) {
            $selectedCategory = $categories->firstWhere(function($cat) use ($categorySlug) {
                return strtolower(str_replace(' ', '-', $cat->Category_name)) === strtolower($categorySlug);
            });
        }
        
        // If no category selected or not found, default to first category
        if (!$selectedCategory) {
            $selectedCategory = $categories->first();
            $categorySlug = strtolower(str_replace(' ', '-', $selectedCategory->Category_name));
        }

// Fetch products for the selected category WITH proper stock checking
        if ($selectedCategory) {
            try {
                $products = DB::table('products as p')
                    ->leftJoin('product_ingredients as pi', 'p.Product_id', '=', 'pi.Product_id')
                    ->leftJoin('inventories as inv', function($join) {
                        $join->on('pi.Ingredient_id', '=', 'inv.Ingredient_id')
                            ->on('p.Product_id', '=', 'inv.Product_id');
                    })
                    ->select(
                        'p.Product_id',
                        'p.Product_name',
                        'p.Price',
                        'p.Image_url',
                        // Calculate how many products can be made based on ingredient stock
                        DB::raw('COALESCE(MIN(FLOOR(inv.RemainingStock / NULLIF(pi.Quantity_used, 0))), 0) as StockQuantity')
                    )
                    ->where('p.Category_id', $selectedCategory->Category_id)
                    ->groupBy('p.Product_id', 'p.Product_name', 'p.Price', 'p.Image_url')
                    ->orderBy('p.Product_id', 'desc')
                    ->get();
                    
                Log::info('Products fetched for category ' . $selectedCategory->Category_name . ': ' . $products->count());
                
                // Log stock info for debugging
                foreach($products as $product) {
                    Log::info('Product: ' . $product->Product_name . ' | Stock: ' . $product->StockQuantity);
                }
                
            } catch (\Exception $e) {
                Log::error('Error fetching products: ' . $e->getMessage());
                // Return empty products on error to prevent page crash
                $products = collect([]);
            }
        }
    } else {
        Log::warning('No categories found in database');
        $categorySlug = '';
    }

    // Debug: Log what we're passing to the view
    Log::info('Passing to view:', [
        'staffName' => $staffName,
        'categories_count' => $categories->count(),
        'products_count' => $products->count(),
        'selectedCategory' => $selectedCategory ? $selectedCategory->Category_name : 'none',
        'categorySlug' => $categorySlug
    ]);

    // ✅ FIXED: Changed from 'cashier.pos' to 'cashier'
    return view('cashier.pos', compact('staffName', 'categories', 'products', 'selectedCategory', 'categorySlug'));
}

    /**
     * ⚠️ LEGACY METHODS - Keep for backward compatibility
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

    public function storeOrder(Request $request)
{
    try {
        DB::beginTransaction();
        
        $orderData = $request->validate([
            'customer_name' => 'required|string',
            'order_type' => 'required|string',
            'total' => 'required|numeric',
            'orders' => 'required|array',
        ]);

        // Get employee ID from session
        $employeeId = Session::get('cashier_id');
        
        if (!$employeeId) {
            return response()->json(['success' => false, 'message' => 'Not logged in'], 401);
        }

        // Create the order
        $orderId = DB::table('orders')->insertGetId([
            'Customer_id' => null, // or create customer record
            'Employee_id' => $employeeId,
            'Customers Name' => $orderData['customer_name'],
            'OrderDate' => now(),
            'TotalAmount' => $orderData['total'],
            'OrderType' => $orderData['order_type'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Process each order item
        foreach ($orderData['orders'] as $item) {
            // Find the product
            $product = DB::table('products')
                ->where('Product_name', $item['name'])
                ->first();
            
            if (!$product) {
                throw new \Exception('Product not found: ' . $item['name']);
            }

            // Insert order item
            DB::table('order_items')->insert([
                'Order_id' => $orderId,
                'Product_id' => $product->Product_id,
                'Quantity' => $item['quantity'],
                'Price' => $item['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Deduct ingredients from inventory
            $ingredients = DB::table('product_ingredients')
                ->where('Product_id', $product->Product_id)
                ->get();

            foreach ($ingredients as $ingredient) {
                $quantityToDeduct = $ingredient->quantity_used * $item['quantity'];
                
                // Update inventory - deduct from RemainingStock
                DB::table('inventories')
                    ->where('Product_id', $product->Product_id)
                    ->where('Ingredient_id', $ingredient->Ingredient_id)
                    ->update([
                        'RemainingStock' => DB::raw("RemainingStock - {$quantityToDeduct}"),
                        'Action' => 'deduct',
                        'DateUsed' => now(),
                        'updated_at' => now(),
                    ]);
                
                Log::info("Deducted {$quantityToDeduct} of ingredient {$ingredient->Ingredient_id} for product {$product->Product_name}");
            }
        }

        DB::commit();
        
        return response()->json([
            'success' => true, 
            'message' => 'Order placed successfully!',
            'order_id' => $orderId
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error placing order: ' . $e->getMessage());
        return response()->json([
            'success' => false, 
            'message' => 'Failed to place order: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Handle logout
     */
    public function logout()
    {
        Session::forget('cashier_id');
        Session::forget('cashier_name');
        return redirect()->route('login.cashier')->with('success', 'Logged out successfully');
    }
}