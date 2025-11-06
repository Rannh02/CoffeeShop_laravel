<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $ingredients = Ingredient::all();
        $suppliers = Supplier::all();

           return view('AdminDashboard.Products', compact(
            'products',
            'categories',
            'ingredients',
            'suppliers',
            'totalPages', 
            'page'));
    }

    // Store a new product
    public function store(Request $request)
{
    Log::info('Product store request received', $request->all());

    $request->validate([
        'Product_name' => 'required|string|max:255',
        'Category_id' => 'required|integer',
        'Price' => 'required|numeric|min:0',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'ingredient_ids' => 'required|array',
        'quantities' => 'required|array',
    ]);

    try {
        $product = new Product();
        $product->Product_name = $request->Product_name;
        $product->Category_id = $request->Category_id;
        $product->Price = $request->Price;

        // ✅ Save first to get product_id
        $product->save();

        // ✅ Handle image upload (after product is created)
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/products'), $filename);
            $product->Image_url = 'images/products/' . $filename;
            $product->save();
        }

        // ✅ Attach ingredients with quantity
        foreach ($request->ingredient_ids as $ingredient_id) {
            $qty = $request->quantities[$ingredient_id] ?? 1;
            $product->ingredients()->attach($ingredient_id, ['quantity_used' => $qty]);
        }

        Log::info('Product saved successfully', ['product_id' => $product->id]);
        return redirect()->route('admin.products')->with('success', 'Product added successfully!');
    } catch (\Exception $e) {
        Log::error('Error saving product: ' . $e->getMessage());
        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to add product: ' . $e->getMessage());
    }
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
