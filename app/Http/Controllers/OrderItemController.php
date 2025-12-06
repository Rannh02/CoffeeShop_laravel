<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderItemController extends Controller
{
    public function index()
    {
        $search = request('search');

        // Fetch order items with left joins so missing product/customer records don't drop rows
        $orderItems = DB::table('order_items')
            ->join('orders', 'order_items.Order_id', '=', 'orders.Order_id')
            ->leftJoin('products', 'order_items.Product_id', '=', 'products.Product_id')
            ->leftJoin('customer', 'orders.Customer_id', '=', 'customer.Customer_id')
            ->select(
                'order_items.OrderItem_id',
                'order_items.Order_id',
                // if customer record exists use customer.Customer_name otherwise fallback to orders.Customer_name
                DB::raw('COALESCE(customer.Customer_name, orders.Customer_name) AS Customer_name'),
                'products.Product_name',
                'order_items.Quantity',
                'order_items.UnitPrice'
            )
            ->when($search, function($query, $search) {
                $query->where('customer.Customer_name', 'like', "%$search%")
                      ->orWhere('products.Product_name', 'like', "%$search%")
                      ->orWhere('order_items.Order_id', 'like', "%$search%")
                      ->orWhere('order_items.OrderItem_id', 'like', "%$search%");
            })
            ->orderBy('order_items.OrderItem_id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('AdminDashboard.OrderItem', compact('orderItems'));
    }
}