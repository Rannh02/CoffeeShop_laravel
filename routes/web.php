<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\OrderItemController;

Route::get('/admin/orderitem', [AdminController::class, 'orderItems'])->name('admin.orderitem');
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

// ✅ All admin routes grouped under prefix "admin"
Route::prefix('admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // ✅ Products (uses default route names like products.index, products.create, etc.)
    Route::resource('products', ProductController::class);

    // ✅ Ingredients
    Route::get('/ingredients', [IngredientController::class, 'index'])->name('admin.ingredients');
    Route::post('/ingredients', [IngredientController::class, 'store'])->name('admin.ingredients.store');
    Route::post('/ingredients/{id}/update', [IngredientController::class, 'update'])->name('admin.ingredients.update');
    Route::delete('/ingredients/{id}', [IngredientController::class, 'destroy'])->name('admin.ingredients.destroy');

    // ✅ Suppliers
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('admin.suppliers');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('admin.suppliers.store');
    Route::post('/suppliers/{id}/update', [SupplierController::class, 'update'])->name('admin.suppliers.update');
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('admin.suppliers.destroy');

    // ✅ Orders (UPDATED - now uses controller)
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders');
    Route::post('/orders', [OrderController::class, 'store'])->name('admin.orders.store');

    // ✅ Placeholder pages (replace with Blade views later)
    Route::view('/orderitem', 'AdminDashboard.OrderItem')->name('admin.orderitem');
    Route::view('/employee', 'AdminDashboard.Employee')->name('admin.employee');
    Route::view('/archived', 'AdminDashboard.Archived')->name('admin.archived');
    Route::view('/inventory', 'AdminDashboard.Inventory')->name('admin.inventory');
    Route::view('/payment', 'AdminDashboard.Payment')->name('admin.payment');
    Route::view('/category', 'AdminDashboard.Category')->name('admin.category');
});