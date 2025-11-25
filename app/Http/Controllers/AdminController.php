<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminController extends Controller
{
    // ✅ Admin Login Form
    public function showLoginForm() {
        return view('LoginSystem.AdminLogin');
    }

    // ✅ Cashier Login Form
    public function showCashierLoginForm() {
        return view('LoginSystem.CashierLogin');
    }

    public function login(Request $request) {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        if ($username === 'admin' && $password === 'admin') {
            Session::put('admin_logged_in', true);
            Session::put('admin_username', $username);
            return redirect()->route('admin.dashboard')->with('success', 'Welcome Admin!');
        }

        return back()->withErrors([
            'credentials' => 'Invalid username or password.',
        ])->withInput($request->only('username'));
    }

    public function dashboard() {
        if (!Session::get('admin_logged_in')) {
            return redirect()->route('login.admin');
        }

        // Total Orders
        $totalOrders = DB::table('orders')->count();

        // Total Income (All Time)
        $totalIncome = DB::table('orders')->sum('TotalAmount') ?? 0;

        // Total Customers
        $totalCustomers = DB::table('orders')->distinct('Customer_id')->count('Customer_id');

        // TOP 5 BEST-SELLING PRODUCTs
        // Get top 5 products by total quantity sold
        $topProducts = DB::table('order_items as oi')
            ->join('products as p', 'oi.Product_id', '=', 'p.Product_id')
            ->select(
                'p.Product_name',
                DB::raw('SUM(oi.Quantity) as total_sold'),
                DB::raw('SUM(oi.Quantity * oi.UnitPrice) as total_revenue')
            )
            ->groupBy('p.Product_id', 'p.Product_name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Prepare data for Chart.js
        $topProductsLabels = $topProducts->pluck('Product_name')->toArray();
        $topProductsValues = $topProducts->pluck('total_sold')->toArray();

      
        // WEEKLY SALES (Current Week: Monday to Sunday
        // Get the start (Monday) and end (Sunday) of the current week
        $startOfWeek = Carbon::now()->startOfWeek(); // Monday
        $endOfWeek = Carbon::now()->endOfWeek(); // Sunday

        // Get sales data for the current week
        $salesData = DB::table('orders')
            ->select(
                DB::raw('DAYNAME(Order_date) as day_name'),
                DB::raw('DATE(Order_date) as order_date'),
                DB::raw('SUM(TotalAmount) as daily_sales')
            )
            ->whereBetween('Order_date', [$startOfWeek, $endOfWeek])
            ->groupBy('day_name', 'order_date')
            ->orderBy('order_date')
            ->get();

        // Create a map of day name to sales
        $salesMap = [];
        foreach ($salesData as $sale) {
            $salesMap[$sale->day_name] = (float)$sale->daily_sales;
        }

        // Define full week (Mon–Sun) and ensure all days are present
        $weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $weeklySalesLabels = [];
        $weeklySalesValues = [];

        foreach ($weekDays as $day) {
            $weeklySalesLabels[] = substr($day, 0, 3); // Mon, Tue, Wed, etc.
            $weeklySalesValues[] = $salesMap[$day] ?? 0; // Default to 0 if no sales
        }

        // Calculate total weekly income
        $weeklyIncome = array_sum($weeklySalesValues);

        // ============================================
        // PREPARE DATA FOR JAVASCRIPT CHARTS
        // ============================================
        $chartData = [
            // Top Products Chart
            'topProductsLabels' => $topProductsLabels,
            'topProductsValues' => $topProductsValues,
            
            // Weekly Sales Chart
            'weeklySalesLabels' => $weeklySalesLabels,
            'weeklySalesValues' => $weeklySalesValues,
            
            // Additional data
            'weeklyIncome' => $weeklyIncome,
            'weekRange' => $startOfWeek->format('M d') . ' - ' . $endOfWeek->format('M d, Y')
        ];

        return view('AdminDashboard.Dashboard', compact(
            'totalOrders',
            'totalIncome',
            'totalCustomers',
            'topProducts',
            'chartData',
            'weeklyIncome'
        ));
    }

    public function showProducts() {
        $fullname = Auth::check() ? Auth::user()->name : 'Admin';
        $products = Product::join('categories', 'products.Category_id', '=', 'categories.Category_id')
                            ->select('products.*', 'categories.Category_name')
                            ->get();
        $categories = Category::all();
        $ingredients = Ingredient::all();
        $suppliers = Supplier::all();

        return view('AdminDashboard.Products', compact('fullname', 'products', 'categories', 'ingredients', 'suppliers'));
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        $request->session()->regenerateToken();
        
        return redirect()->route('welcome')->with('success', 'Logged out successfully');
    }
}