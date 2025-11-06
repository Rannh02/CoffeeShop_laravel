<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::orderBy('Category_id', 'ASC')->get();
            $fullname = Auth::user()->name ?? 'Admin';
            
            return view('AdminDashboard.Category', compact('categories', 'fullname'));
        } catch (\Exception $e) {
            Log::error('Category Index Error: ' . $e->getMessage());
            return back()->with('error', 'Error loading categories: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'Category_name' => 'required|string|max:255',
        ]);

        try {
            Category::create([
                'Category_name' => $request->input('Category_name'),
            ]);

            return back()->with('success', 'Category added successfully.');
        } catch (\Exception $e) {
            Log::error('Category Store Error: ' . $e->getMessage());
            return back()->with('error', 'Error adding category: ' . $e->getMessage());
        }
    }
}
