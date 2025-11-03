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

// ✅ Cashier login page
Route::get('/login/cashier', function () {
    return view('LoginSystem.CashierLogin');
})->name('login.cashier');

// ✅ Admin login routes
Route::get('/login/admin', [AdminController::class, 'showLoginForm'])->name('login.admin');
Route::post('/login/admin', [AdminController::class, 'login'])->name('admin.login.submit');
Route::post('/logout/admin', [AdminController::class, 'logout'])->name('admin.logout');

// ✅ API endpoint for POS/Cashier to submit orders with payment (JSON)
Route::post('/api/orders/payment', [PaymentController::class, 'storeOrderWithPayment'])
    ->name('api.orders.payment');

// ============================================
// 🆕 CASHIER ROUTES - DYNAMIC POS SYSTEM
// ============================================
Route::prefix('cashier')->group(function () {
    // Login routes (public)
    Route::get('/login/cashier', [CashierController::class, 'showLoginForm'])
        ->name('cashier.login.form');

    Route::post('/login/cashier', [CashierController::class, 'login'])
        ->name('cashier.login');

    // Protected routes with middleware
    Route::middleware('cashier.auth')->group(function () {
        // 🆕 DYNAMIC POS ROUTE - Shows all categories dynamically
        Route::get('/pos', [CashierController::class, 'index'])
            ->name('cashier.pos');
        
        // 🆕 Logout route for cashier
        Route::get('/logout', [CashierController::class, 'logout'])
            ->name('cashier.logout');

        // ⚠️ LEGACY ROUTES - You can keep these for backward compatibility
        // or remove them if you want to use only the dynamic route
        Route::get('/coffee', [CashierController::class, 'showCoffee'])
            ->name('cashier.coffee');
        Route::get('/tea', [CashierController::class, 'showTea'])
            ->name('cashier.tea');
        Route::get('/cold', [CashierController::class, 'showColdDrinks'])
            ->name('cashier.cold');
        Route::get('/pastries', [CashierController::class, 'showPastries'])
            ->name('cashier.pastries');
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

    // ✅ Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('admin.inventory');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('admin.inventory.store');

    // ✅ Payment
    Route::get('/payment', [PaymentController::class, 'index'])->name('admin.payment');
});