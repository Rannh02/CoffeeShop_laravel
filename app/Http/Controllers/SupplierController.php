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
        return view('AdminDashboard.Supplier', compact('suppliers'));
    }

    // Store new supplier
    public function store(Request $request)
    {
        $request->validate([
            'Supplier_name' => 'required|string|max:255',
            'Contact_number' => 'required|string|max:20',
            'Address' => 'required|string|max:255',
        ]);

        Supplier::create([
            'Supplier_name' => $request->Supplier_name,
            'Contact_number' => $request->Contact_number,
            'Address' => $request->Address,
            'Status' => 'active',
        ]);

        return redirect()->back()->with('success', 'Supplier added successfully.');
    }

    // Update existing supplier
   public function update(Request $request, $Supplier_id)
{
    $supplier = Supplier::findOrFail($Supplier_id);

    $request->validate([
        'Supplier_name' => 'required|string|max:255',
        'Contact_number' => 'required|string|max:20',
        'Address' => 'required|string|max:255',
    ]);

    $supplier->update([
        'Supplier_name' => $request->Supplier_name,
        'Contact_number' => $request->Contact_number,
        'Address' => $request->Address,
    ]);

    return redirect()->route('suppliers.index')->with('status_message', 'Supplier updated successfully!');
}

    // Archive / Restore
  public function archive(Request $request, $Supplier_id)
{
    try {
        $supplier = Supplier::findOrFail($Supplier_id);
        
        // Toggle status between active and archived
        $supplier->Status = $supplier->Status === 'active' ? 'archived' : 'active';
        $supplier->save();

        return response()->json([
            'success' => true,
            'message' => 'Supplier status updated successfully.',
            'new_status' => $supplier->Status
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update supplier status: ' . $e->getMessage()
        ], 500);
    }
}
}
