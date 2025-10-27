<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderItemController extends Controller
{
    public function index()
    {
        // Fetch order items from the database with pagination
        $orderItems = DB::table('order_items') // ✅ Changed from 'orderitem' to 'order_items'
            ->join('orders', 'order_items.Order_id', '=', 'orders.Order_id')
            ->join('products', 'order_items.Product_id', '=', 'products.Product_id')
            ->join('customer', 'orders.Customer_id', '=', 'customer.Customer_id') // Note: Check if table is 'costumer' or 'customer'
            ->select(
                'order_items.OrderItem_id',
                'order_items.Order_id',
                'custumer.CustomerName',
                'products.Product_name',
                'order_items.Quantity',
                'order_items.UnitPrice as Price_sale' // ✅ Changed from Price_sale to UnitPrice and aliased it
            )
            ->paginate(10); // ✅ Added pagination (10 items per page)

        // Pass the variable to the view
        return view('AdminDashboard.OrderItem', compact('orderItems'));
    }
}