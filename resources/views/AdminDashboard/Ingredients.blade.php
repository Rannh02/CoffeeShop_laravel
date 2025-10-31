<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('vite.svg') }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin - Ingredients</title>

    {{-- Styles --}}
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/ingredients.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/ingredientsModal.css') }}">
    <link rel="stylesheet" href="{{ asset('products.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<div id="app">
    <!-- Header -->
    <header class="header">
        <div class="header-left">
            <div class="logo"><span>Berde Kopi</span></div>
        </div>
        <div class="header-right">
            <div class="admin-profile">
                <span class="time" id="currentTime">Time</span>
                <i class="bi bi-person-circle"></i>
                <span><strong style="color:green; font-weight:bolder;">Admin:</strong> {{ $fullname ?? 'Admin' }}</span>
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
                <a href="{{ route('admin.archived') }}" class="nav-item"><i class="bi bi-person-x"></i> Archived</a>
                <a href="{{ route('admin.inventory') }}" class="nav-item"><i class="bi bi-cart-check"></i> Inventory</a>
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
                <h1 class="page-title">Ingredients</h1>
                <button id="openProductModal" class="add-product-btn">
                        <i class="bi bi-plus-circle">   Add Employee</i>
                </button>
            </div>

            <!-- Ingredients Table -->
            <div class="table-container">
                <table class="products-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Supplier</th>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($ingredients as $ingredient)
                        <tr>
                            <td>{{ $ingredient->Ingredient_id }}</td>
                            <td>{{ $ingredient->supplier->Supplier_name ?? 'N/A' }}</td>
                            <td>{{ $ingredient->Ingredient_name }}</td>
                            <td>{{ $ingredient->Quantity }}</td>
                            <td>{{ $ingredient->Unit }}</td>
                            <td style="color: green;">Available</td>
                            <td>
                                <form action="{{ route('ingredients.destroy', $ingredient->Ingredient_id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal for Adding Ingredient -->
    <div id="ProductModal" class="modal-overlay" style="display:none;">
        <form action="{{ route('ingredients.store') }}" method="POST" class="modal-form">
            @csrf
            <h1>Add Ingredient</h1>

            <label>Ingredient Name</label>
            <input type="text" name="Ingredient_name" required>

            <label>Quantity</label>
            <input type="number" min="1" name="Quantity" required>

            <label>Unit</label>
            <input type="text" name="Unit" required>

            <label>Supplier</label>
            <select id="Supplier_id" name="Supplier_id" required>
                <option value="">-- Select Supplier --</option>
                @foreach ($suppliers as $sup)
                    <option value="{{ $sup->Supplier_id }}">{{ $sup->Supplier_name }}</option>
                @endforeach
            </select>

            <div class="btn-group">
                <button type="submit" class="AddBtn">Add Ingredient</button>
                <button type="button" id="closeProductModal" class="CancelBtn">Cancel</button>
            </div>
        </form>
    </div>
</div>
<script src="{{ asset('Javascripts/RealTime.js') }}"></script>
<script src= "{{ asset('Javascripts/ingredientsDelete.js')}}"> </script>
<script src="{{ asset('Javascripts/ingredientsModal.js') }}"></script>
</body>
</html>
