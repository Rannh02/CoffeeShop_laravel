<?php
// app/Http/Controllers/EmployeeController.php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{   
    public function index()
    {
        $search = request('search');

        $employees = Employee::where('Status', 'Active')
            ->when($search, function($query, $search) {
                $query->where('First_name', 'like', "%$search%")
                      ->orWhere('Last_name', 'like', "%$search%")
                      ->orWhere('Employee_id', 'like', "%$search%")
                      ->orWhere('Position', 'like', "%$search%")
                      ->orWhere('Cashier_Account', 'like', "%$search%")
                      ->orWhere('Gender', 'like', "%$search%")
                      ->orWhere('Contact_number', 'like', "%$search%");
            })
            ->orderBy('Date_of_Hire', 'desc')
            ->paginate(5)
            ->withQueryString();

        $fullname = Auth::user()->name ?? 'Admin'; // Adjust based on your auth setup
        
        return view('AdminDashboard.Employee', compact('employees', 'fullname'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'cashierAccount' => 'required|string|max:100|unique:employee,Cashier_Account',
            'password' => 'required|string|min:6',
            'gender' => 'required|in:Male,Female',
            'contact' => 'required|string|max:15',
            'position' => 'required|in:Cashier,Manager,Staff',
        ]);

        Employee::create([
            'First_name' => $validated['firstname'],
            'Last_name' => $validated['lastname'],
            'Cashier_Account' => $validated['cashierAccount'],
            'Password' => Hash::make($validated['password']),
            'Gender' => $validated['gender'],
            'Contact_number' => $validated['contact'],
            'Position' => $validated['position'],
            'Date_of_Hire' => now(),
            'Status' => 'Active'
        ]);

        return redirect()->route('admin.employee')
            ->with('success', 'Employee added successfully!');
    }

    public function archive($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->update(['Status' => 'Archived']);

        return response()->json(['success' => true, 'message' => 'Employee archived successfully']);
    }
    public function archived(){
        $employees = Employee::where('Status', 'Archived')->paginate(4);
        $fullname = Auth::user()->name ?? 'Admin'; // Consistent with index
        
        return view('AdminDashboard.Archived', compact('employees', 'fullname'));
    }
    public function restore(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->update(['Status' => 'Active']);

        // If the request expects JSON (AJAX), return JSON. Otherwise redirect back to archived list.
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Employee restored successfully']);
        }

        return redirect()->route('admin.archived')->with('success', 'Employee restored successfully');
    }
}   