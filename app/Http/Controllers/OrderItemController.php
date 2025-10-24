<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderItemController extends Controller
{
    public function index()
    {
        // Fetch order items from the database
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

        // ✅ Pass the variable to the view
        return view('AdminDashboard.OrderItem', compact('orderItems'));
    }

}