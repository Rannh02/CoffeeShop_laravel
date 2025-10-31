<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;

class IngredientController extends Controller
{
    // Show all ingredients
    public function index()
    {
        $fullname = Auth::user()->name ?? 'Admin';
        $ingredients = Ingredient::with('supplier')->get();
        $suppliers = Supplier::all();

        return view('AdminDashboard.Ingredients', compact('fullname', 'ingredients', 'suppliers'));
    }

    // Store new ingredient
    public function store(Request $request)
    {
        $request->validate([
            'Ingredient_name' => 'required|string|max:255',
            'Quantity' => 'required|numeric|min:0',
            'Unit' => 'required|string|max:50',
            'Supplier_id' => 'nullable|exists:suppliers,Supplier_id',
        ]);

        Ingredient::create($request->all());

        return redirect()->back()->with('success', 'Ingredient added successfully.');
    }

    // Update an existing ingredient
    public function update(Request $request, $id)
    {
        $ingredient = Ingredient::findOrFail($id);

        $request->validate([
            'Ingredient_name' => 'required|string|max:255',
            'Quantity' => 'required|numeric|min:0',
            'Unit' => 'required|string|max:50',
            'Supplier_id' => 'nullable|exists:suppliers,Supplier_id',
        ]);

        $ingredient->update($request->all());

        return redirect()->back()->with('success', 'Ingredient updated successfully.');
    }

    // Delete an ingredient
    public function destroy($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        $ingredient->delete();

        return redirect()->back()->with('success', 'Ingredient deleted successfully.');
    }
}
