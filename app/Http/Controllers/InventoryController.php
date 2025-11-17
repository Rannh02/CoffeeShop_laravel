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
     * Display inventory listing with pagination
     */
    public function index()
    {
        $fullname = Session::get('fullname', 'Admin');

        $search = request('search');

        // Paginate inventory with product + ingredient relationships
        $inventories = Inventory::with(['product', 'ingredient'])
            ->when($search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('Inventory_id', 'like', "%$search%")
                      ->orWhereHas('product', function($qp) use ($search) {
                          $qp->where('Product_name', 'like', "%$search%");
                      })
                      ->orWhereHas('ingredient', function($qi) use ($search) {
                          $qi->where('Ingredient_name', 'like', "%$search%");
                      });
                });
            })
            ->orderBy('DateUsed', 'desc')
            ->paginate(5)
            ->withQueryString(); // Preserve query parameters in pagination links

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
            'Product_id'     => 'required|exists:products,Product_id',
            'Ingredient_id'  => 'required|exists:ingredients,Ingredient_id',
            'QuantityUsed'   => 'required|numeric|min:0',
            'Action'         => 'required|in:add,deduct'
        ]);

        DB::beginTransaction();
        try {
            $inventory = Inventory::where('Product_id', $request->Product_id)
                                  ->where('Ingredient_id', $request->Ingredient_id)
                                  ->first();

            if ($inventory) {
                // Update existing record
                if ($request->Action === 'add') {
                    $inventory->RemainingStock += $request->QuantityUsed;
                } else {
                    if ($inventory->RemainingStock < $request->QuantityUsed) {
                        return back()->with('error', 'Insufficient stock to deduct.');
                    }
                    $inventory->RemainingStock -= $request->QuantityUsed;
                    $inventory->QuantityUsed += $request->QuantityUsed;
                }

                $inventory->Action = $request->Action;
                $inventory->DateUsed = now();
                $inventory->save();

            } else {
                // Create new inventory record
                Inventory::create([
                    'Product_id'     => $request->Product_id,
                    'Ingredient_id'  => $request->Ingredient_id,
                    'QuantityUsed'   => $request->Action === 'deduct' ? $request->QuantityUsed : 0,
                    'RemainingStock' => $request->Action === 'add' ? $request->QuantityUsed : 0,
                    'Action'         => $request->Action,
                    'DateUsed'       => now()
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