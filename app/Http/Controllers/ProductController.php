<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    // Display the list of products
    public function index(Request $request)
{
    $page = $request->input('page', 1);
    $perPage = 5;

    $products = DB::table('products')
        ->leftJoin('categories', 'products.Category_id', '=', 'categories.Category_id')
        ->select(
            'products.Product_id',
            'products.Category_id as Category_id',
            'products.Product_name',
            'products.Price',
            'products.Image_url',
            'categories.Category_name'
        )
        ->skip(($page - 1) * $perPage)
        ->take($perPage)
        ->get();

    $totalProducts = DB::table('products')->count();
    $totalPages = ceil($totalProducts / $perPage);

    $categories = Category::all();
    $ingredients = Ingredient::all();

    return view('AdminDashboard.Products', compact(
        'products', 'categories', 'ingredients', 'totalPages', 'page'
    ));
}


    // Store a new product
    public function store(Request $request)
{
    Log::info('Product store request received', $request->all());

    $request->validate([
    'Product_name' => 'required|string|max:255',
    'Category_id' => 'required|integer|exists:categories,Category_id',
    'Price' => 'required|numeric|min:0',
    'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    'ingredient_ids' => 'required|array|min:1',
    'quantities' => 'required|array|min:1',
    ]);


    try {
        $product = new Product();
        $product->Product_name = $request->Product_name;
        $product->Category_id = $request->Category_id;
        $product->Price = $request->Price;
        $product->save();

        // ✅ Handle image
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

        Log::info('Product saved successfully', ['product_id' => $product->Product_id]);
        return redirect()->route('products.index')->with('success', 'Product added successfully!');

    } catch (\Exception $e) {
        Log::error('Error saving product: ' . $e->getMessage());
        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to add product: ' . $e->getMessage());
    }
}


    // Edit product
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('AdminDashboard.EditProduct', compact('product', 'categories'));
    }

    // Update product
    public function update(Request $request, $id)
    {
        $request->validate([
            'Product_name' => 'required|string|max:255',
            'Category_id' => 'required|integer',
            'Price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $product = Product::findOrFail($id);
        $product->Product_name = $request->Product_name;
        $product->Category_id = $request->Category_id;
        $product->Price = $request->Price;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/products'), $filename);
            $product->Image_url = 'images/products/' . $filename;
        }

        $product->save();

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    // Delete product
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->Image_url && file_exists(public_path($product->Image_url))) {
            unlink(public_path($product->Image_url));
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }
}
