<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class IngredientController extends Controller
{
    // Show all ingredients
    public function index()
    {
        $fullname = Auth::user()->name ?? 'Admin';

        $search = request('search');

        $ingredients = Ingredient::when($search, function($query, $search) {
                $query->where('Ingredient_name', 'like', "%$search%")
                      ->orWhere('Ingredient_id', 'like', "%$search%")
                      ->orWhere('Unit', 'like', "%$search%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('AdminDashboard.Ingredients', compact('fullname', 'ingredients'));
    }

    // Store a new ingredient
    public function store(Request $request)
{
    $request->validate([
        'Ingredient_name' => 'required|string|max:255|unique:ingredients,Ingredient_name',
        'StockQuantity' => 'required|numeric|min:0',
        'Unit' => 'required|string',
        'ReorderLevel' => 'required|numeric|min:0',
    ]);

    try {
        DB::table('ingredients')->insert([
            'Ingredient_name' => $request->Ingredient_name,
            'StockQuantity' => $request->StockQuantity,
            'Unit' => $request->Unit,
            'ReorderLevel' => $request->ReorderLevel,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info("Ingredient added: {$request->Ingredient_name}, Stock: {$request->StockQuantity} {$request->Unit}");
        return redirect()->route('admin.ingredients')->with('success', 'Ingredient added successfully!');
        
    } catch (\Exception $e) {
        Log::error('Error adding ingredient: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('error', 'Failed to add ingredient: ' . $e->getMessage());
    }
}

    // Update an existing ingredient
   public function update(Request $request, $id)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'StockQuantity' => 'required|numeric|min:0',
                'ReorderLevel' => 'required|numeric|min:0',
            ]);

            // Find ingredient
            $ingredient = DB::table('ingredients')
                ->where('Ingredient_id', $id)
                ->first();

            if (!$ingredient) {
                return redirect()->back()->with('error', 'Ingredient not found.');
            }

            // Update ingredient
            DB::table('ingredients')
                ->where('Ingredient_id', $id)
                ->update([
                    'StockQuantity' => $validated['StockQuantity'],
                    'ReorderLevel' => $validated['ReorderLevel'],
                    'updated_at' => now()
                ]);

            return redirect()->route('admin.ingredients')
                ->with('success', 'Ingredient updated successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update ingredient: ' . $e->getMessage());
        }
    }


    public function products()
{
    return $this->belongsToMany(Product::class, 'product_ingredients', 'Ingredient_id', 'Product_id')
                ->withPivot('Quantity_used')
                ->withTimestamps();
}


    // Delete an ingredient
    public function destroy($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        $ingredient->delete();

        return redirect()->back()->with('success', 'Ingredient deleted successfully.');
    }
}