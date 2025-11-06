<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Display payment listing
     */
    public function index()
    {
        $fullname = Session::get('fullname', 'Admin');
        
        // Get all payments ordered by date descending
        // Try-catch to handle table issues gracefully
        try {
            $payments = Payment::orderBy('PaymentDate', 'desc')->get();
        } catch (\Exception $e) {
            // If table doesn't exist or query fails, return empty collection
            $payments = collect([]);
            Log::error('Payment query error: ' . $e->getMessage());
        }

        return view('AdminDashboard.Payment', compact('payments', 'fullname'));
    }

    /**
     * Store a new order with payment (JSON API endpoint)
     * This handles the POS/Cashier system submitting orders
     */
    public function storeOrderWithPayment(Request $request)
    {
        // Validate if this is a JSON request
        if (!$request->isJson()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request format. JSON expected.'
            ], 400);
        }

        // Validate incoming data
        $validator = Validator::make($request->all(), [
            'customerName' => 'nullable|string|max:255',
            'orderType' => 'nullable|string|max:50',
            'totalAmount' => 'required|numeric|min:0',
            'orders' => 'required|array|min:1',
            'orders.*.name' => 'required|string',
            'orders.*.quantity' => 'required|integer|min:1',
            'orders.*.price' => 'required|numeric|min:0',
            'paymentMethod' => 'nullable|string|max:50',
            'amountPaid' => 'nullable|numeric|min:0',
            'transactionReference' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Get validated data with defaults
            $customerName = $request->input('customerName', 'Guest');
            $orderType = $request->input('orderType', 'Dine In');
            $totalAmount = $request->input('totalAmount');
            $orders = $request->input('orders');
            $paymentMethod = $request->input('paymentMethod', 'Cash');
            $amountPaid = $request->input('amountPaid', $totalAmount);
            $transactionReference = $request->input('transactionReference');

            // Create the order
            $order = Order::create([
                'CustomerName' => $customerName,
                'OrderType' => $orderType,
                'TotalAmount' => $totalAmount,
                'OrderDate' => now()
            ]);

            // Create order items
            foreach ($orders as $item) {
                OrderItem::create([
                    'Order_id' => $order->Order_id,
                    'ProductName' => $item['name'],
                    'Quantity' => $item['quantity'],
                    'Price' => $item['price']
                ]);
            }

            // Create payment record
            Payment::create([
                'Order_id' => $order->Order_id,
                'Payment_method' => $paymentMethod,
                'Amount_Paid' => $amountPaid,
                'PaymentDate' => now(),
                'TransactionReference' => $transactionReference
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order and payment recorded successfully.',
                'order_id' => $order->Order_id
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }
}