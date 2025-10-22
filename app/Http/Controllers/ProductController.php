<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    // Display the list of products
    public function index(Request $request)
    {
        $page = $request->input('page', 1);

        // ✅ How many items per page
        $perPage = 5;

        // ✅ Get all products count
        $totalProducts = Product::count();

        // ✅ Calculate total pages
        $totalPages = ceil($totalProducts / $perPage);

        // ✅ Get the correct subset of products for the current page
        $products = Product::skip(($page - 1) * $perPage)
                           ->take($perPage)
                           ->get();

        // Retrieve all categories for the dropdown
        $categories = Category::all();

        return view('AdminDashboard.Products', compact('products', 'categories'));
    }

    // Store a new product
    public function store(Request $request)
    {
        $request->validate([
            'Product_name' => 'required|string|max:255',
            'Category_id' => 'required|integer',
            'Product_price' => 'required|numeric|min:0',
            'Product_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $product = new Product();
        $product->Product_name = $request->Product_name;
        $product->Category_id = $request->Category_id;
        $product->Product_price = $request->Product_price;

        // Handle image upload
        if ($request->hasFile('Product_image')) {
            $file = $request->file('Product_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/products'), $filename);
            $product->Product_image = $filename;
        }

        $product->save();

        return redirect()->route('products.index')->with('success', 'Product added successfully!');
    }

    // Edit product form
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('AdminDashboard.EditProduct', compact('product', 'categories'));
    }

    // Update product data
    public function update(Request $request, $id)
    {
        $request->validate([
            'Product_name' => 'required|string|max:255',
            'Category_id' => 'required|integer',
            'Product_price' => 'required|numeric|min:0',
            'Product_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $product = Product::findOrFail($id);
        $product->Product_name = $request->Product_name;
        $product->Category_id = $request->Category_id;
        $product->Product_price = $request->Product_price;

        // Handle new image upload
        if ($request->hasFile('Product_image')) {
            $file = $request->file('Product_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/products'), $filename);
            $product->Product_image = $filename;
        }

        $product->save();

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    // Delete a product
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Optional: delete the product image from public folder
        if ($product->Product_image && file_exists(public_path('images/products/' . $product->Product_image))) {
            unlink(public_path('images/products/' . $product->Product_image));
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }
}
