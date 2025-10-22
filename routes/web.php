<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// ✅ Welcome page
Route::get('/', function () {
    return view('LoginSystem.WelcomeLogin');
})->name('welcome');

// ✅ Cashier login page
Route::get('/login/cashier', function () {
    return view('LoginSystem.CashierLogin');
})->name('login.cashier');

// ✅ Admin login page
Route::get('/login/admin', function () {
    return view('LoginSystem.AdminLogin');
})->name('login.admin');

// ✅ Admin routes
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

// ✅ Admin dashboard route
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

//routes for product resource controller
// Route::prefix('admin')->group(function () {
//     Route::resource('products', ProductController::class);
// });

// //side bar links routes
// Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.products');
// Route::post('/admin/products', [ProductController::class, 'store'])->name('products.store');




Route::get('/admin/orders', function() { return 'Orders Page'; })->name('admin.orders');
Route::get('/admin/orderitem', function() { return 'OrderItem Page'; })->name('admin.orderitem');
Route::get('/admin/employee', function() { return 'Employee Page'; })->name('admin.employee');
Route::get('/admin/archived', function() { return 'Archived Page'; })->name('admin.archived');
Route::get('/admin/inventory', function() { return 'Inventory Page'; })->name('admin.inventory');
Route::get('/admin/ingredients', function() { return 'Ingredients Page'; })->name('admin.ingredients');
Route::get('/admin/supplier', function() { return 'Supplier Page'; })->name('admin.supplier');
Route::get('/admin/payment', function() { return 'Payment Page'; })->name('admin.payment');
Route::get('/admin/category', function() { return 'Category Page'; })->name('admin.category');