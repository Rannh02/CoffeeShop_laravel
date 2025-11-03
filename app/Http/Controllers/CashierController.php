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
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Attempt to find the employee
        $employee = DB::table('employee')
            ->where('Email', $request->email)
            ->first();

        // Verify credentials (adjust based on your auth logic)
        if ($employee && Hash::check($request->password, $employee->Password ?? '')) {
            Session::put('cashier_id', $employee->employee_id);
            Session::put('cashier_name', $employee->First_name . ' ' . $employee->Last_name);
            
            return redirect()->route('cashier.pos');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
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
                    ->where('employee_id', Session::get('cashier_id'))
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
            $categories = DB::table('category')
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
                    return strtolower(str_replace(' ', '-', $cat->category_name)) === strtolower($categorySlug);
                });
            }
            
            // If no category selected or not found, default to first category
            if (!$selectedCategory) {
                $selectedCategory = $categories->first();
                $categorySlug = strtolower(str_replace(' ', '-', $selectedCategory->category_name));
            }

            // Fetch products for the selected category WITH stock info
            if ($selectedCategory) {
                try {
                    $products = DB::table('product as p')
                        ->leftJoin('inventory as i', 'p.Product_id', '=', 'i.Product_id')
                        ->select(
                            'p.Product_id',
                            'p.Product_name',
                            'p.Price',
                            'p.Image',
                            DB::raw('COALESCE(i.QuantityInStock, 0) as QuantityInStock')
                        )
                        ->where('p.Category_id', $selectedCategory->Category_id)
                        ->orderBy('p.Product_id', 'desc')
                        ->get();

                    Log::info('Products fetched for category ' . $selectedCategory->category_name . ': ' . $products->count());
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
            'selectedCategory' => $selectedCategory ? $selectedCategory->category_name : 'none',
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