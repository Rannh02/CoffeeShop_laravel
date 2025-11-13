<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Inventory</title>
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/inventory.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/InventoryModal.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<div id="app">
    <!-- Header -->
    <header class="header">
        <div class="header-left">
            <div class="logo">
                <span>Berde Kopi</span>
            </div>
        </div>
        <div class="header-right">
            <div class="admin-profile">
                <span class="time">Time</span>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="currentColor"/>
                    <path d="M12 14C7.58172 14 4 17.5817 4 22H20C20 17.5817 16.4183 14 12 14Z" fill="currentColor"/>
                </svg>
                <span><strong style="color:green; font-weight:bolder;">Admin: </strong>{{ $fullname }}</span>
            </div>
        </div>
    </header>

    <div class="main-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="nav-item"><i class="bi bi-list"></i> Dashboard</a>
                <a href="{{ route('products.index') }}" class="nav-item"><i class="bi bi-bag"></i> Products</a>
                <a href="{{ route('admin.orders') }}" class="nav-item"><i class="bi bi-bag-check-fill"></i> Orders</a>
                <a href="{{ route('admin.orderitem') }}" class="nav-item"><i class="bi bi-basket"></i> OrderItem</a>
                <a href="{{ route('admin.employee') }}" class="nav-item"><i class="bi bi-person-circle"></i> Employee</a>
                <a href="{{ route('admin.archived') }}" class="nav-item"><i class="bi bi-person-x"></i> Employee Archived</a>
                <a href="{{ route('admin.inventory') }}" class="nav-item active"><i class="bi bi-cart-check"></i> Inventory</a>
                <a href="{{ route('admin.ingredients') }}" class="nav-item"><i class="bi bi-check2-square"></i> Ingredients</a>
                <a href="{{ route('suppliers.index') }}" class="nav-item"><i class="bi bi-box-fill"></i> Supplier</a>
                <a href="{{ route('admin.payment') }}" class="nav-item"><i class="bi bi-cash-coin"></i> Payment</a>
                <a href="{{ route('admin.category') }}" class="nav-item"><i class="bi bi-tags"></i> Category</a>
                <a href="{{ route('admin.logout') }}" class="nav-item logout">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="products-header">
                <h1 class="page-title">Inventory</h1>
                <button id="stockInBtn" class="add-product-btn">
                        <i class="bi bi-plus-circle">   Stock In</i>
                </button>
            </div>

            @if(session('success'))
                <div class="alert alert-success" style="padding: 10px; margin: 10px 0; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger" style="padding: 10px; margin: 10px 0; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px;">
                    {{ session('error') }}
                </div>
            @endif

            <div class="table-container">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Inventory_id</th>
                            <th>Product_id</th>
                            <th>Ingredient_id</th>
                            <th>QuantityUsed</th>
                            <th>RemainingStock</th>
                            <th>Action</th>
                            <th>DateUsed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventories as $inventory)
                            <tr>
                                <td>{{ $inventory->Inventory_id }}</td>
                                <td>{{ $inventory->Product_id }}</td>
                                <td>{{ $inventory->QuantityInStock }}</td>
                                <td>{{ $inventory->ReorderLevel }}</td>
                                <td>{{ $inventory->LastRestockDate }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center;">No inventory records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal for Adding Inventory -->
    <div id="ProductModal" class="modal-overlay" style="display: none;">
        <form action="{{ route('admin.inventory.store') }}" method="POST">
            @csrf
            <h1>Stock In</h1>

            <p>Select Product</p>
            <select name="Product_id" required>
                <option value="">-- Select Product --</option>
                @foreach($products as $product)
                    <option value="{{ $product->Product_id }}">
                        {{ $product->Product_name }}
                    </option>
                @endforeach
            </select>
            @error('Product_id')
                <span class="error" style="color: red; font-size: 12px;">{{ $message }}</span>
            @enderror

            <p>Quantity to Add</p>
            <input type="number" name="QuantityToAdd" required min="1" value="{{ old('QuantityToAdd') }}">
            @error('QuantityToAdd')
                <span class="error" style="color: red; font-size: 12px;">{{ $message }}</span>
            @enderror

            <div class="btn-group">
                <button type="submit" class="AddBtn">Add Stock</button>
                <button type="button" id="closeProductModal" class="CancelBtn">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("ProductModal");
    const closeBtn = document.getElementById("closeProductModal");
    const stockInBtn = document.getElementById("stockInBtn");

    if (stockInBtn) {
        stockInBtn.addEventListener("click", () => {
            modal.style.display = "flex";
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener("click", () => {
            modal.style.display = "none";
        });
    }

    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });
});
</script>
<script type="module" src="{{ asset('JS_Dashboard/DashboardsTime.js') }}"></script>
</body>
</html>