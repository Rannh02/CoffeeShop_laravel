<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class CashierController extends Controller
{
    // Show cashier login page
    public function showLoginForm()
    {
        return view('LoginSystem.CashierLogin');
    }

    // Handle cashier login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Find cashier in Employee table
        $employee = Employee::where('Cashier_Account', $credentials['username'])
                            ->where('Position', 'Cashier')
                            ->where('Status', 'Active')
                            ->first();

        if (!$employee || !Hash::check($credentials['password'], $employee->Password)) {
            return back()->with('error', 'Invalid username or password.');
        }

        return back()->withErrors(['username' => 'Cashier Account is not valid.'])->withInput();
    }

    // Dashboard - redirects to coffee page
    public function dashboard()
    {
        return redirect()->route('cashier.coffee');
    }

    // Show Coffee products
    public function showCoffee()
    {
        $staffName = session('cashier', 'Cashier');
        $products = Product::where('Category_id', 'Coffee')
                          ->orderBy('Product_name')
                          ->get();
        
        return view('cashier.coffee', compact('staffName', 'products'));
    }

    // Show Tea products
    public function showTea()
    {
        $staffName = session('cashier', 'Cashier');
        $products = Product::where('Category', 'Tea')
                          ->orderBy('Product_name')
                          ->get();
        
        return view('cashier.tea', compact('staffName', 'products'));
    }

    // Show Cold Drinks products
    public function showColdDrinks()
    {
        $staffName = session('cashier', 'Cashier');
        $products = Product::where('Category', 'Cold Drinks')
                          ->orderBy('Product_name')
                          ->get();
        
        return view('cashier.cold', compact('staffName', 'products'));
    }

    // Show Pastries products
    public function showPastries()
    {
        $staffName = session('cashier', 'Cashier');
        $products = Product::where('Category', 'Pastries')
                          ->orderBy('Product_name')
                          ->get();
        
        return view('cashier.pastries', compact('staffName', 'products'));
    }

    // Logout
    public function logout()
    {
        session()->forget(['cashier', 'cashier_id']);
        return redirect()->route('cashier.login.form');
    }
}