<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');

            // Query with optional search
            $query = Category::orderBy('Category_id', 'ASC');

            if (!empty($search)) {
                $query->where('Category_id', 'LIKE', "%$search%")
                      ->orWhere('Category_name', 'LIKE', "%$search%");
            }

            // PAGINATION (change 7 to any number of items per page)
            $categories = $query->paginate(5);

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
