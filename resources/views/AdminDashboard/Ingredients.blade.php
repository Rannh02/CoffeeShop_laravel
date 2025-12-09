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
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/products.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .search-container {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .search-input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        .search-btn {
            padding: 10px 20px;
            background-color: #8f8f8fff;
            color: #1f2937;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        
        .search-btn i {
            font-size: 15px; 
            -webkit-text-stroke: 1px;
        }
        
        .search-btn:hover {
            background-color: #747474ff;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Action buttons styling */
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .update-btn {
            background-color: #3B82F6;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .update-btn:hover {
            background-color: #2563EB;
            transform: translateY(-1px);
        }

        .delete-btn {
            background-color: #EF4444;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .delete-btn:hover {
            background-color: #DC2626;
            transform: translateY(-1px);
        }
    </style>
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
                <a href="{{ route('admin.archived') }}" class="nav-item"><i class="bi bi-person-x"></i> Employee Archived</a>
                <a href="{{ route('admin.inventory') }}" class="nav-item"><i class="bi bi-cart-check"></i> Inventory</a>
                <a href="{{ route('admin.ingredients') }}" class="nav-item active"><i class="bi bi-check2-square"></i> Ingredients</a>
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
                <button class="add-product-btn" id="openProductModal">Add Ingredients</button>
            </div>

            <!-- Search Bar -->
            <div class="search-container">
                <input 
                    type="text" 
                    id="searchInput" 
                    class="search-input" 
                    placeholder="Search for Ingredients."
                    value="{{ request('search') }}"
                >
                <button class="search-btn" onclick="performSearch()">
                    <i class="bi bi-search"></i>
                </button>
            </div>

            <!-- Ingredients Table -->
            <div class="table-container">
                <table class="products-table">
                    <thead>
                    <tr>
                        <th>Ingredient ID</th>
                        <th>Ingredient Name</th>
                        <th>Unit</th>
                        <th>Stock Quantity</th>
                        <th>Reorder Level</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($ingredients as $ingredient)
                        <tr>
                            <td>{{ $ingredient->Ingredient_id }}</td>
                            <td>{{ $ingredient->Ingredient_name }}</td>
                            <td>{{ $ingredient->Unit }}</td>
                            <td>{{ $ingredient->StockQuantity }}</td>
                            <td>{{ $ingredient->ReorderLevel }}</td>

                            <!-- Status Check -->
                            @if ($ingredient->StockQuantity <= $ingredient->ReorderLevel)
                                <td style="color: red; font-weight:bold;">Low Stock</td>
                            @else
                                <td style="color: green;">Available</td>
                            @endif

                            <td>
                                <div class="action-buttons">
                                    <button 
                                        class="update-btn" 
                                        onclick="openUpdateModal({{ $ingredient->Ingredient_id }}, '{{ $ingredient->Ingredient_name }}', {{ $ingredient->StockQuantity }}, {{ $ingredient->ReorderLevel }})">
                                        Update
                                    </button>
                                    <form action="{{ route('ingredients.destroy', $ingredient->Ingredient_id) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-btn">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center;">No ingredients found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                <div class="pagination-container">
                    {{-- Previous Button --}}
                    @if ($ingredients->currentPage() > 1)
                        <a class="page-btn" href="{{ $ingredients->previousPageUrl() }}">Previous</a>
                    @else
                        <span class="page-btn disabled">Previous</span>
                    @endif

                    {{-- Page Numbers --}}
                    @for ($i = 1; $i <= $ingredients->lastPage(); $i++)
                        @if ($i == $ingredients->currentPage())
                            <span class="page-number active">{{ $i }}</span>
                        @else
                            <a class="page-number" href="{{ $ingredients->url($i) }}">{{ $i }}</a>
                        @endif
                    @endfor

                    {{-- Next Button --}}
                    @if ($ingredients->currentPage() < $ingredients->lastPage())
                        <a class="page-btn" href="{{ $ingredients->nextPageUrl() }}">Next</a>
                    @else
                        <span class="page-btn disabled">Next</span>
                    @endif
                </div>
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

            <label>Stock Quantity</label>
            <input type="number" step="0.01" min="0" name="StockQuantity" required>

            <label>Unit</label>
            <select name="Unit" required>
                <option value="" disabled selected>Select Unit</option>
                <option value="Box">Box</option>
                <option value="Gram">Gram</option>
                <option value="Milliliter">Milliliter</option>
                <option value="Piece">Piece</option>
                <option value="Pack">Pack</option>
                <option value="Teaspoon">Teaspoon</option>
                <option value="Tablespoon">Tablespoon</option>
            </select>

            <label>Reorder Level</label>
            <input type="number" step="0.1" min="0" name="ReorderLevel" value="10" required>

            <div class="btn-group">
                <button type="submit" class="AddBtn">Add Ingredient</button>
                <button type="button" id="closeProductModal" class="CancelBtn">Cancel</button>
            </div>
        </form>
    </div>

    <!-- Modal for Updating Ingredient -->
    <div id="UpdateModal" class="modal-overlay" style="display:none;">
        <form id="updateForm" method="POST" class="modal-form">
            @csrf
            @method('PUT')
            <h1>Update Ingredient</h1>

            <input type="hidden" id="updateIngredientId" name="Ingredient_id">

            <label>Ingredient Name</label>
            <input type="text" id="updateIngredientName" name="Ingredient_name" readonly style="background: #f3f4f6;">

            <label>Stock Quantity</label>
            <input type="number" step="0.01" min="0" id="updateStockQuantity" name="StockQuantity" required>

            <label>Reorder Level</label>
            <input type="number" step="0.1" min="0" id="updateReorderLevel" name="ReorderLevel" required>

            <div class="btn-group">
                <button type="submit" class="AddBtn">Update Ingredient</button>
                <button type="button" id="closeUpdateModal" class="CancelBtn">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('Javascripts/RealTime.js') }}"></script>
<script src="{{ asset('Javascripts/ingredientsDelete.js') }}"></script>
<script src="{{ asset('Javascripts/ingredientsModal.js') }}"></script>

<script>
// Search functionality
function performSearch() {
    const searchValue = document.getElementById('searchInput').value;
    const currentUrl = new URL(window.location.href);
    
    if (searchValue.trim()) {
        currentUrl.searchParams.set('search', searchValue);
        currentUrl.searchParams.set('page', '1');
    } else {
        currentUrl.searchParams.delete('search');
    }
    
    window.location.href = currentUrl.toString();
}

// Allow search on Enter key
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        performSearch();
    }
});

// Open Update Modal
function openUpdateModal(id, name, stock, reorder) {
    document.getElementById('updateIngredientId').value = id;
    document.getElementById('updateIngredientName').value = name;
    document.getElementById('updateStockQuantity').value = stock;
    document.getElementById('updateReorderLevel').value = reorder;
    
    // Set form action
    document.getElementById('updateForm').action = `/admin/ingredients/${id}`;
    
    document.getElementById('UpdateModal').style.display = 'flex';
}

// Close Update Modal
document.getElementById('closeUpdateModal').addEventListener('click', function() {
    document.getElementById('UpdateModal').style.display = 'none';
});

// Close modal when clicking outside
document.getElementById('UpdateModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.style.display = 'none';
    }
});
</script>
</body>
</html>