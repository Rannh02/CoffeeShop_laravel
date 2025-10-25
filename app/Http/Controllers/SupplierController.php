<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    // ✅ Show all suppliers
    public function index()
    {
        $suppliers = Supplier::all();
        return view('AdminDashboard.Supplier', compact('suppliers'));
    }

    // ✅ Store a new supplier
    public function store(Request $request)
    {
        $request->validate([
            'Supplier_name'   => 'required|string|max:255',
            'Contact_number'  => 'nullable|string|max:20',
            'Address'         => 'nullable|string|max:255',
        ]);

        Supplier::create([
            'Supplier_name'  => $request->Supplier_name,
            'Contact_number' => $request->Contact_number,
            'Address'        => $request->Address,
            'Status'         => 'active',
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Supplier added successfully.');
    }

    // ✅ Update supplier
    public function update(Request $request, $Supplier_id)
    {
        $request->validate([
            'Supplier_name'   => 'required|string|max:255',
            'Contact_number'  => 'nullable|string|max:20',
            'Address'         => 'nullable|string|max:255',
        ]);

        $supplier = Supplier::findOrFail($Supplier_id);
        $supplier->update($request->only(['Supplier_name', 'Contact_number', 'Address']));

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    // ✅ Archive / Restore supplier
    public function archive($Supplier_id)
    {
        $supplier = Supplier::findOrFail($Supplier_id);
        $supplier->Status = $supplier->Status === 'active' ? 'inactive' : 'active';
        $supplier->save();

        return redirect()->route('suppliers.index')->with('success', 'Supplier status updated.');
    }

    // ✅ Delete supplier
    public function destroy($Supplier_id)
    {
        $supplier = Supplier::findOrFail($Supplier_id);
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}
