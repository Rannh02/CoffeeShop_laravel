<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    // =========================
    // ✅ ADMIN LOGIN
    // =========================
    public function showLoginForm() {
        return view('LoginSystem.AdminLogin');
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

    // =========================
    // ✅ CASHIER LOGIN
    // =========================
    public function showCashierLoginForm()
    {
        return view('LoginSystem.CashierLogin');
    }

    public function cashierLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $employee = DB::table('employee')
            ->where('username', $request->username)
            ->where('password', $request->password)
            ->first();

        if ($employee) {
            Session::put('cashier_logged_in', true);
            Session::put('cashier_name', $employee->Employee_name);

            return view('Cashier.coffee')->with('success', 'Welcome, ' . $employee->Employee_name . '!');
        }

        return back()->withErrors([
            'credentials' => 'Invalid username or password.',
        ])->withInput();
    }

    // =========================
    // ✅ ADMIN DASHBOARD
    // =========================
    public function dashboard() {
        if (!Session::get('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $totalOrders = DB::table('order')->count();
        $totalIncome = DB::table('order')->sum('TotalAmount') ?? 0;
        $totalCustomers = DB::table('order')
            ->whereNotNull('Customer_id')
            ->selectRaw('COUNT(DISTINCT Customer_id) as total')
            ->value('total');

        $topProducts = DB::table('orderitem as oi')
            ->join('product as p', 'oi.Product_id', '=', 'p.Product_id')
            ->select('p.Product_name', DB::raw('SUM(oi.Quantity) as total_sold'))
            ->groupBy('p.Product_id', 'p.Product_name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $salesData = DB::table('order')
            ->select(DB::raw('DAYNAME(OrderDate) as day_name'), DB::raw('SUM(TotalAmount) as daily_sales'))
            ->whereRaw('YEARWEEK(OrderDate, 1) = YEARWEEK(CURDATE(), 1)')
            ->groupBy('day_name')
            ->pluck('daily_sales', 'day_name')
            ->toArray();

        $weekDays = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        $chartSales = [];
        foreach ($weekDays as $day) {
            $chartSales[$day] = isset($salesData[$day]) ? (float)$salesData[$day] : 0;
        }

        Log::info('Dashboard Data:', [
            'totalOrders' => $totalOrders,
            'totalIncome' => $totalIncome,
            'totalCustomers' => $totalCustomers,
            'topProducts' => $topProducts->count(),
            'chartSales' => count($chartSales)
        ]);

        return view('Admin.DashboardAdmin', compact(
            'totalOrders',
            'totalIncome',
            'totalCustomers',
            'topProducts',
            'chartSales'
        ));
    }

    // =========================
    // ✅ ORDER ITEMS (ADMIN)
    // =========================
    public function orderItems()
    {
        if (!Session::get('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $orderItems = DB::table('orderitem')
            ->join('orders', 'orderitem.Order_id', '=', 'orders.Order_id')
            ->join('products', 'orderitem.Product_id', '=', 'products.Product_id')
            ->join('costumer', 'orders.Costumer_id', '=', 'costumer.Costumer_id')
            ->select(
                'orderitem.OrderItem_id',
                'orderitem.Order_id',
                'costumer.CustomerName',
                'products.Product_name',
                'orderitem.Quantity',
                'orderitem.Price_sale'
            )
            ->get();

        return view('AdminDashboard.OrderItem', compact('orderItems'));
    }

    // =========================
    // ✅ LOGOUT (ADMIN + CASHIER)
    // =========================
    public function logout() {
        Session::flush();
        return redirect()->route('admin.login')->with('success', 'Logged out successfully!');
    }
}
