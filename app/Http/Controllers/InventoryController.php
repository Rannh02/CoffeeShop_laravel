<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Ingredient;
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
        $fullname = Session::get('fullname', 'Admin');
        
        // Get all inventory records with product and ingredient info
        $inventories = Inventory::with(['product', 'ingredient'])->get();
        
        $products = Product::orderBy('Product_name', 'asc')->get();
        $ingredients = Ingredient::orderBy('Ingredient_name', 'asc')->get();

        return view('AdminDashboard.Inventory', compact('inventories', 'products', 'ingredients', 'fullname'));
    }

    /**
     * Store new inventory stock or deduct usage
     */
    public function store(Request $request)
    {
        $request->validate([
            'Product_id' => 'required|exists:products,Product_id',
            'Ingredient_id' => 'required|exists:ingredients,Ingredient_id',
            'QuantityUsed' => 'required|numeric|min:0',
            'Action' => 'required|in:add,deduct'
        ]);

        DB::beginTransaction();
        try {
            $inventory = Inventory::where('Product_id', $request->Product_id)
                                  ->where('Ingredient_id', $request->Ingredient_id)
                                  ->first();

            if ($inventory) {
                // Update existing inventory
                if ($request->Action === 'add') {
                    $inventory->RemainingStock += $request->QuantityUsed;
                } else {
                    $inventory->RemainingStock -= $request->QuantityUsed;
                    $inventory->QuantityUsed += $request->QuantityUsed;
                }
                $inventory->Action = $request->Action;
                $inventory->DateUsed = now();
                $inventory->save();
            } else {
                // Create new inventory record
                Inventory::create([
                    'Product_id' => $request->Product_id,
                    'Ingredient_id' => $request->Ingredient_id,
                    'QuantityUsed' => $request->Action === 'deduct' ? $request->QuantityUsed : 0,
                    'RemainingStock' => $request->Action === 'add' ? $request->QuantityUsed : 0,
                    'Action' => $request->Action,
                    'DateUsed' => now()
                ]);
            }

            DB::commit();
            return redirect()->route('admin.inventory')
                             ->with('success', 'Inventory updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                             ->with('error', 'Failed to update inventory: ' . $e->getMessage());
        }
    }
}
