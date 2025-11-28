<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
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
            'transactionReference' => 'nullable|string|max:100',
            'isPWD' => 'nullable|boolean',
            'isSenior' => 'nullable|boolean'
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
            // Normalize order type to match DB enum values ('Dine In' or 'Takeout')
            $otype = trim(strtolower($orderType));
            if (in_array($otype, ['take out', 'take-out', 'takeout'])) {
                $orderType = 'Takeout';
            } elseif (in_array($otype, ['dine in', 'dine-in', 'dinein'])) {
                $orderType = 'Dine In';
            }
            
            $totalAmount = $request->input('totalAmount');
            
            // Apply discount if PWD or Senior Citizen
            $isPWD = $request->input('isPWD', false);
            $isSenior = $request->input('isSenior', false);
            $discountedAmount = $totalAmount;
            $discountPercent = '';
            
            if ($isPWD || $isSenior) {
                $discountedAmount = $totalAmount * 0.88; // 12% discount
                $discountPercent = $isPWD ? ' (PWD)' : ' (Senior Citizen)';
                Log::info('Discount applied: ' . $discountPercent . ' - Original: ' . $totalAmount . ', Discounted: ' . $discountedAmount);
            }
            
            $orders = $request->input('orders');
            $paymentMethod = $request->input('paymentMethod', 'Cash');
            $amountPaid = $request->input('amountPaid', $discountedAmount);
            $transactionReference = $request->input('transactionReference');

            // Create the order with the discounted amount
            $order = Order::create([
                'Customer_name' => $customerName,
                'Order_Type' => $orderType,
                'TotalAmount' => $discountedAmount,
                'Order_date' => now()
            ]);

            // Create order items: try to resolve Product_id by name when possible
            foreach ($orders as $item) {
                $product = Product::where('Product_name', $item['name'])->first();
                $productId = $product ? $product->Product_id : null;

                OrderItem::create([
                    'Order_id' => $order->Order_id,
                    'Product_id' => $productId,
                    'Quantity' => $item['quantity'],
                    'UnitPrice' => $item['price']
                ]);
            }

            // Format transaction reference for display/storage
            $txRef = null;
            $pm = strtolower(trim($paymentMethod));
            if ($pm === 'card') {
                if (!empty($transactionReference)) {
                    $txRef = 'Card Number: ' . $transactionReference;
                } else {
                    $txRef = 'Card';
                }
            } elseif ($pm === 'gcash' || $pm === 'e-wallet' || $pm === 'ewallet') {
                if (!empty($transactionReference)) {
                    $txRef = 'Reference Number: ' . $transactionReference;
                } else {
                    $txRef = 'GCash';
                }
            } else {
                // default to Cash
                $txRef = 'Cash';
            }
            
            // Append discount info to transaction reference if discount was applied
            if ($isPWD || $isSenior) {
                $txRef .= $discountPercent;
            }

            // Create payment record (column names match migration)
            Payment::create([
                'Order_id' => $order->Order_id,
                'PaymentMethod' => $paymentMethod,
                'AmountPaid' => $amountPaid,
                'PaymentDate' => now(),
                'TransactionReference' => $txRef
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