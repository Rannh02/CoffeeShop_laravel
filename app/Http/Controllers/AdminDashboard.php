<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
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

    public function dashboard() {
        if (!Session::get('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        // Total Orders
        $totalOrders = DB::table('order')->count();

        // Total Income
        $totalIncome = DB::table('order')->sum('TotalAmount') ?? 0;

        // Total Customers - Count Distinct Customer IDs
        $totalCustomers = DB::table('order')
            ->whereNotNull('Customer_id')
            ->selectRaw('COUNT(DISTINCT Customer_id) as total')
            ->value('total');

        // Top 5 Best-Selling Products
        $topProducts = DB::table('orderitem as oi')
            ->join('product as p', 'oi.Product_id', '=', 'p.Product_id')
            ->select('p.Product_name', DB::raw('SUM(oi.Quantity) as total_sold'))
            ->groupBy('p.Product_id', 'p.Product_name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Last 7 Days Sales (grouped by weekday)
        $salesData = DB::table('order')
            ->select(DB::raw('DAYNAME(OrderDate) as day_name'), DB::raw('SUM(TotalAmount) as daily_sales'))
            ->whereRaw('YEARWEEK(OrderDate, 1) = YEARWEEK(CURDATE(), 1)')
            ->groupBy('day_name')
            ->pluck('daily_sales', 'day_name')
            ->toArray();

        // Define full week (Mon–Sun)
        $weekDays = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        $chartSales = [];
        foreach ($weekDays as $day) {
            $chartSales[$day] = isset($salesData[$day]) ? (float)$salesData[$day] : 0;
        }

        // Debug - Check if variables are set
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

    public function logout() {
        Session::flush();
        return redirect()->route('admin.login')->with('success', 'Logged out successfully!');
    }
}