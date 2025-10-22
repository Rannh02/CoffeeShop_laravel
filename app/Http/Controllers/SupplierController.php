<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    // Show all suppliers
    public function index()
    {
        $suppliers = Supplier::all();
        return view('AdminDashboard.Suppliers', compact('suppliers'));
    }

    // Store a new supplier
    public function store(Request $request)
    {
        $request->validate([
            'Supplier_name' => 'required|string|max:255',
            'Contact_number' => 'nullable|string|max:20',
            'Address' => 'nullable|string|max:255',
        ]);

        Supplier::create($request->all());

        return redirect()->back()->with('success', 'Supplier added successfully.');
    }

    // Update supplier
    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'Supplier_name' => 'required|string|max:255',
            'Contact_number' => 'nullable|string|max:20',
            'Address' => 'nullable|string|max:255',
        ]);

        $supplier->update($request->all());

        return redirect()->back()->with('success', 'Supplier updated successfully.');
    }

    // Delete supplier
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->back()->with('success', 'Supplier deleted successfully.');
    }
}
