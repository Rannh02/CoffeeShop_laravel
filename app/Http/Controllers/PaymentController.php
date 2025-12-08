<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments with search functionality
     */
    public function index(Request $request)
    {
        $query = Payment::query();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            
            $query->where(function($q) use ($search) {
                $q->where('Payment_id', 'LIKE', "%{$search}%")
                  ->orWhere('Order_id', 'LIKE', "%{$search}%")
                  ->orWhere('PaymentMethod', 'LIKE', "%{$search}%")
                  ->orWhere('TransactionReference', 'LIKE', "%{$search}%")
                  ->orWhere('AmountPaid', 'LIKE', "%{$search}%");
            });
        }

        // Paginate results (10 per page)
        $payments = $query->orderBy('Payment_id', 'desc')->paginate(10);

        // Get admin info
        $fullname = session('fullname', 'Admin');

        return view('admin.payment', compact('payments', 'fullname'));
    }

    /**
     * Show the form for creating a new payment
     */
    public function create()
    {
        $orders = Order::all();
        $fullname = session('fullname', 'Admin');
        
        return view('admin.payment-create', compact('orders', 'fullname'));
    }

    /**
     * Store a newly created payment in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Order_id' => 'required|exists:orders,Order_id',
            'PaymentMethod' => 'required|string|max:50',
            'AmountPaid' => 'required|numeric|min:0',
            'TransactionReference' => 'nullable|string|max:255'
        ]);

        try {
            Payment::create($validated);
            
            return redirect()->route('admin.payment')
                ->with('success', 'Payment created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified payment
     */
    public function show($id)
    {
        $payment = Payment::with('order')->findOrFail($id);
        $fullname = session('fullname', 'Admin');
        
        return view('admin.payment-show', compact('payment', 'fullname'));
    }

    /**
     * Show the form for editing the specified payment
     */
    public function edit($id)
    {
        $payment = Payment::findOrFail($id);
        $orders = Order::all();
        $fullname = session('fullname', 'Admin');
        
        return view('admin.payment-edit', compact('payment', 'orders', 'fullname'));
    }

    /**
     * Update the specified payment in storage
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'Order_id' => 'required|exists:orders,Order_id',
            'PaymentMethod' => 'required|string|max:50',
            'AmountPaid' => 'required|numeric|min:0',
            'TransactionReference' => 'nullable|string|max:255'
        ]);

        try {
            $payment = Payment::findOrFail($id);
            $payment->update($validated);
            
            return redirect()->route('admin.payment')
                ->with('success', 'Payment updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified payment from storage
     */
    public function destroy($id)
    {
        try {
            $payment = Payment::findOrFail($id);
            $payment->delete();
            
            return redirect()->route('admin.payment')
                ->with('success', 'Payment deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting payment: ' . $e->getMessage());
        }
    }
}