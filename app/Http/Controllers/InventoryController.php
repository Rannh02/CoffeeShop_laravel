<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class InventoryController extends Controller
{
    /**
     * Display inventory listing
     */
    public function index()
    {
        // Get admin fullname from session (matching your existing pattern)
        $fullname = Session::get('fullname', 'Admin');
        
        // Get all inventory records
        $inventories = Inventory::all();
        
        // Get all products for the dropdown
        $products = Product::orderBy('Product_name', 'asc')->get();

        return view('AdminDashboard.Inventory', compact('inventories', 'products', 'fullname'));
    }

    /**
     * Store new inventory stock
     */
    public function store(Request $request)
    {
        $request->validate([
            'Product_id' => 'required|exists:product,Product_id',
            'QuantityInStock' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();
        try {
            // Check if inventory record exists for this product
            $inventory = Inventory::where('Product_id', $request->Product_id)->first();

            if ($inventory) {
                // Update existing inventory
                $inventory->QuantityInStock += $request->QuantityToAdd;
                $inventory->LastRestockDate = now();
                $inventory->save();
            } else {
                // Create new inventory record
                Inventory::create([
                    'Product_id' => $request->Product_id,
                    'QuantityInStock' => $request->QuantityToAdd,
                    'ReorderLevel' => 10, // Default value
                    'LastRestockDate' => now()
                ]);
            }

            DB::commit();
            return redirect()->route('admin.inventory')
                ->with('success', 'Stock added successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to add stock: ' . $e->getMessage());
        }
    }
}