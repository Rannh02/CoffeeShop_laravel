<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CoffeeController extends Controller
{
    public function showCoffee()
{
    $staffName = auth()->user()->name ?? 'Cashier';
    $products = Product::where('Category', 'Coffee')->get();
    return view('cashier.coffee', compact('staffName', 'products'));
}

}
