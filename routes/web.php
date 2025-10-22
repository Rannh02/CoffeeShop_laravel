<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\SupplierController;

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

// ✅ Admin dashboard
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

// ✅ Admin Product routes
Route::prefix('admin')->group(function () {
    Route::resource('products', ProductController::class)->names([
        'index' => 'admin.products',
        'create' => 'admin.products.create',
        'store' => 'admin.products.store',
        'show' => 'admin.products.show',
        'edit' => 'admin.products.edit',
        'update' => 'admin.products.update',
        'destroy' => 'admin.products.destroy',
    ]);
});


// ✅ Ingredient routes
Route::prefix('admin')->group(function () {
    Route::get('/ingredients', [IngredientController::class, 'index'])->name('admin.ingredients');
    Route::post('/ingredients', [IngredientController::class, 'store'])->name('admin.ingredients.store');
    Route::post('/ingredients/{id}/update', [IngredientController::class, 'update'])->name('admin.ingredients.update');
    Route::delete('/ingredients/{id}', [IngredientController::class, 'destroy'])->name('admin.ingredients.destroy');
});

// ✅ Supplier routes
Route::prefix('admin')->group(function () {
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('admin.suppliers');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('admin.suppliers.store');
    Route::post('/suppliers/{id}/update', [SupplierController::class, 'update'])->name('admin.suppliers.update');
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('admin.suppliers.destroy');
});

// ✅ Placeholder pages (optional)
Route::get('/admin/orders', fn() => 'Orders Page')->name('admin.orders');
Route::get('/admin/orderitem', fn() => 'OrderItem Page')->name('admin.orderitem');
Route::get('/admin/employee', fn() => 'Employee Page')->name('admin.employee');
Route::get('/admin/archived', fn() => 'Archived Page')->name('admin.archived');
Route::get('/admin/inventory', fn() => 'Inventory Page')->name('admin.inventory');
Route::get('/admin/payment', fn() => 'Payment Page')->name('admin.payment');
Route::get('/admin/category', fn() => 'Category Page')->name('admin.category');
