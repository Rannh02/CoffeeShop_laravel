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
    </style>
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
                <span class="time" id="currentTime">Time</span>
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
                <h1 class="page-title">Inventory Tracking</h1>
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

            <div class="table-container">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Inventory ID</th>
                            <th>Product</th>
                            <th>Ingredient</th>
                            <th>Quantity Used</th>
                            <th>Remaining Stock</th>
                            <th>Action</th>
                            <th>Date Used</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventories as $inventory)
                            <tr>
                                <td>{{ $inventory->Inventory_id }}</td>
                                <td>{{ $inventory->product->Product_name ?? 'N/A' }}</td>
                                <td>
                                    @if($inventory->ingredient)
                                        {{ $inventory->ingredient->Ingredient_name }}
                                        <small style="color:#666">({{ $inventory->ingredient->Unit }})</small>
                                    @else
                                        <span style="color:#999">N/A</span>
                                    @endif
                                </td>
                                <td>{{ number_format($inventory->QuantityUsed, 2) }}</td>
                                <td>
                                    <span style="font-weight:600; color:{{ $inventory->RemainingStock > 10 ? '#28a745' : ($inventory->RemainingStock > 5 ? '#ffc107' : '#dc3545') }};">
                                        {{ number_format($inventory->RemainingStock, 2) }}
                                    </span>
                                </td>
                                <td>
                                    @if($inventory->Action === 'add')
                                        <span style="background:#28a745;color:white;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:600;">Added</span>
                                    @elseif($inventory->Action === 'deduct')
                                        <span style="background:#dc3545;color:white;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:600;">Deducted</span>
                                    @else
                                        <span style="background:#6c757d;color:white;padding:4px 12px;border-radius:12px;font-size:12px;">{{ strtoupper($inventory->Action ?? 'N/A') }}</span>
                                    @endif
                                </td>
                                <td>{{ $inventory->DateUsed ? \Carbon\Carbon::parse($inventory->DateUsed)->format('M d, Y h:i A') : 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center;padding:40px;color:#999;">
                                    <i class="bi bi-inbox" style="font-size:48px;display:block;margin-bottom:10px;"></i>
                                    No inventory records found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination-container">
                    {{-- Previous Button --}}
                    @if ($inventories->currentPage() > 1)
                        <a class="page-btn" href="{{ $inventories->previousPageUrl() }}">Previous</a>
                    @else
                        <span class="page-btn disabled">Previous</span>
                    @endif

                    {{-- Page Numbers --}}
                    @for ($i = 1; $i <= $inventories->lastPage(); $i++)
                        @if ($i == $inventories->currentPage())
                            <span class="page-number active">{{ $i }}</span>
                        @else
                            <a class="page-number" href="{{ $inventories->url($i) }}">{{ $i }}</a>
                        @endif
                    @endfor

                    {{-- Next Button --}}
                    @if ($inventories->currentPage() < $inventories->lastPage())
                        <a class="page-btn" href="{{ $inventories->nextPageUrl() }}">Next</a>
                    @else
                        <span class="page-btn disabled">Next</span>
                    @endif
                </div>
            </div>

        </main>
    </div>
</div>
<script src="{{ asset('Javascripts/RealTime.js') }}"></script>
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
</script>
</body>
</html> 