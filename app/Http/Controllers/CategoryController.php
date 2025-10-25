<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::orderBy('Category_id', 'ASC')->get();
            $fullname = Auth::user()->name ?? 'Admin';
            
            return view('AdminDashboard.Category', compact('categories', 'fullname'));
        } catch (\Exception $e) {
            \Log::error('Category Index Error: ' . $e->getMessage());
            return back()->with('error', 'Error loading categories: ' . $e->getMessage());
        }
    }
}