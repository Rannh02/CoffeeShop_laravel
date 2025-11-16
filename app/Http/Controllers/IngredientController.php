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
        $ingredients = Ingredient::all(); // no supplier relation now

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
        $ingredient = Ingredient::findOrFail($id);

        $request->validate([
            'Ingredient_name' => 'required|string|max:255',
            'Unit' => 'nullable|string|max:50',
            'StockQuantity' => 'required|numeric|min:0',
            'ReorderLevel' => 'required|integer|min:0',
        ]);

        $ingredient->update([
            'Ingredient_name' => $request->Ingredient_name,
            'Unit' => $request->Unit,
            'StockQuantity' => $request->StockQuantity,
            'ReorderLevel' => $request->ReorderLevel,
        ]);

        return redirect()->back()->with('success', 'Ingredient updated successfully.');
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
