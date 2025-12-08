<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PaymentController;

// ✅ Welcome page
Route::get('/', function () {
    return view('LoginSystem.WelcomeLogin');
})->name('welcome');

// ✅ Admin login routes
Route::get('/login/admin', [AdminController::class, 'showLoginForm'])->name('login.admin');
Route::post('/login/admin', [AdminController::class, 'login'])->name('admin.login.submit');
Route::get('/logout/admin', [AdminController::class, 'logout'])->name('admin.logout');

// ✅ API endpoint for POS/Cashier to submit orders with payment (JSON)
Route::post('/api/orders/payment', [PaymentController::class, 'storeOrderWithPayment'])
    ->name('api.orders.payment');

// ============================================
// 🆕 CASHIER ROUTES - DYNAMIC POS SYSTEM
// ============================================
// ============================================
// CASHIER ROUTES
// ============================================
Route::prefix('cashier')->name('cashier.')->group(function () {
    // Public routes
    Route::get('/login', [CashierController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [CashierController::class, 'login'])->name('login');

    Route::get('/cashier/check-stock/{productId}/{quantity}', [CashierController::class, 'checkStock'])
    ->name('cashier.check.stock');
    
    // Protected routes (require cashier authentication)
    Route::middleware('cashier.auth')->group(function () {
        Route::get('/pos', [CashierController::class, 'index'])->name('pos');
        Route::post('/place-order', [CashierController::class, 'storeOrder'])->name('placeOrder');
        Route::get('/logout', [CashierController::class, 'logout'])->name('logout');
        
        // Legacy category routes
        Route::get('/coffee', [CashierController::class, 'showCoffee'])->name('coffee');
        Route::get('/tea', [CashierController::class, 'showTea'])->name('tea');
        Route::get('/cold', [CashierController::class, 'showColdDrinks'])->name('cold');
        Route::get('/pastries', [CashierController::class, 'showPastries'])->name('pastries');
    });
});

// ============================================
// ADMIN ROUTES
// ============================================
Route::prefix('admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // ✅ Products
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');

    // ✅ Ingredients
    Route::prefix('admin')->group(function () {
        Route::resource('ingredients', IngredientController::class);
    });
    Route::get('/ingredients', [IngredientController::class, 'index'])->name('admin.ingredients');
    Route::post('/ingredients', [IngredientController::class, 'store'])->name('admin.ingredients.store');
    Route::post('/ingredients/{id}/update', [IngredientController::class, 'update'])->name('admin.ingredients.update');
    Route::delete('/ingredients/{id}', [IngredientController::class, 'destroy'])->name('admin.ingredients.destroy');

    // ✅ Suppliers
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::post('/suppliers/{Supplier_id}/update', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::post('/suppliers/{Supplier_id}/archive', [SupplierController::class, 'archive'])->name('suppliers.archive');
    Route::delete('/suppliers/{Supplier_id}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

    // ✅ Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders');
    Route::post('/orders', [OrderController::class, 'store'])->name('admin.orders.store');
    Route::post('/orders/store', [OrderController::class, 'storeOrder'])->name('orders.store');
    // to get all the products in POS
    Route::get('/admin/pos', [OrderController::class, 'showPOS'])->name('admin.pos');

    // ✅ Order Items
    Route::get('/orderitem', [OrderItemController::class, 'index'])->name('admin.orderitem');

    // ✅ Employees
    Route::get('/employee', [EmployeeController::class, 'index'])->name('admin.employee');
    Route::post('/employee', [EmployeeController::class, 'store'])->name('admin.employee.store');
    Route::post('/employee/{id}/archive', [EmployeeController::class, 'archive'])->name('admin.employee.archive');
    Route::get('/archived', [EmployeeController::class, 'archived'])->name('admin.archived');
    Route::post('/employee/{id}/restore', [EmployeeController::class, 'restore'])->name('admin.employee.restore');

    // ✅ Category
    Route::get('/category', [CategoryController::class, 'index'])->name('admin.category');
    Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
    // Update & Delete for Category
    Route::post('/category/{Category_id}/update', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/category/{Category_id}', [CategoryController::class, 'destroy'])->name('category.destroy');

    // ✅ Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('admin.inventory');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('admin.inventory.store');

    // ✅ Payment
    Route::get('/payment', [PaymentController::class, 'index'])->name('admin.payment');
});