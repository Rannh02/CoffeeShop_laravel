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
        
        // Initialize with empty collections to prevent undefined variable errors
        $categories = collect([]);
        $products = collect([]);
        $selectedCategory = null;
        
        // Fetch all categories for navigation
        try {
            // table name is 'categories'
            $categories = DB::table('categories')
                ->select('Category_id', 'Category_name')
                ->orderBy('Category_id')
                ->get();
            
            Log::info('Categories fetched: ' . $categories->count());
        } catch (\Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());
            return view('cashier.pos', compact('staffName', 'categories', 'products', 'selectedCategory', 'categorySlug'));
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

            // Fetch products for the selected category WITH stock info
            if ($selectedCategory) {
                try {
                    $products = DB::table('products as p')
                        ->leftJoin('inventory as i', 'p.Product_id', '=', 'i.Product_id')
                        ->select(
                            'p.Product_id',
                            'p.Product_name',
                            'p.Price',
                            'p.Image_url',
                            DB::raw('COALESCE(i.QuantityInStock, 0) as QuantityInStock')
                        )
                        ->where('p.Category_id', $selectedCategory->Category_id)
                        ->orderBy('p.Product_id', 'desc')
                        ->get();

                    Log::info('Products fetched for category ' . $selectedCategory->Category_name . ': ' . $products->count());
                } catch (\Exception $e) {
                    Log::error('Error fetching products: ' . $e->getMessage());
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
    // Example structure
    $orderData = $request->validate([
        'customer_name' => 'required|string',
        'order_type' => 'required|string',
        'total' => 'required|numeric',
        'orders' => 'required|array',
    ]);

    // Save order in database (you can expand with your models)
    $order = Order::create([
        'Customer_name' => $orderData['customer_name'],
        'Order_type' => $orderData['order_type'],
        'Total_amount' => $orderData['total'],
    ]);

    foreach ($orderData['orders'] as $item) {
        OrderItem::create([
            'order_id' => $order->id,
            'product_name' => $item['name'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
        ]);

        // Deduct ingredients from inventory (if product has ingredients)
        $product = Product::where('Product_name', $item['name'])->first();
        if ($product) {
            foreach ($product->ingredients as $ingredient) {
                $ingredient->Stock -= ($ingredient->pivot->Quantity_used * $item['quantity']);
                $ingredient->save();
            }
        }
    }

    return response()->json(['success' => true, 'message' => 'Order placed successfully!']);
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