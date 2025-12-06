<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Orders</title>
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/Orders.css') }}">
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

        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
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
                    <span><strong style="color:green; font-weight:bolder;">Admin: </strong>{{ session('fullname') }}</span>
                </div>
            </div>
        </header>

        <div class="main-container">
            <!-- Sidebar -->
            <aside class="sidebar">
                <nav class="sidebar-nav">
                    <a href="{{ route('admin.dashboard') }}" class="nav-item"><i class="bi bi-list"></i> Dashboard</a>
                <a href="{{ route('products.index') }}" class="nav-item"><i class="bi bi-bag"></i> Products</a>
                <a href="{{ route('admin.orders') }}" class="nav-item active"><i class="bi bi-bag-check-fill"></i> Orders</a>
                <a href="{{ route('admin.orderitem') }}" class="nav-item"><i class="bi bi-basket"></i> OrderItem</a>
                <a href="{{ route('admin.employee') }}" class="nav-item"><i class="bi bi-person-circle"></i> Employee</a>
                <a href="{{ route('admin.archived') }}" class="nav-item"><i class="bi bi-person-x"></i> Employee Archived</a>
                <a href="{{ route('admin.inventory') }}" class="nav-item"><i class="bi bi-cart-check"></i> Inventory</a>
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
                    <h1 class="page-title">Orders</h1>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Search Bar -->
                <div class="search-container">
                    <input 
                        type="text" 
                        id="searchInput" 
                        class="search-input" 
                        placeholder="Search for Orders."
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
                                <th>Order ID</th>
                                <th>Customer ID</th>
                                <th>Employee ID</th>
                                <th>Customer Name</th>
                                <th>Total Amount</th>
                                <th>Order Type</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>{{ $order->Order_id }}</td>
                                    <td>{{ $order->Customer_id }}</td>
                                    <td>{{ $order->Employee_id }}</td>
                                    <td>{{ $order->{'Customer_name'} }}</td>
                                    <td>₱{{ number_format($order->TotalAmount, 2) }}</td>
                                    <td>{{ $order->Order_Type }}</td>
                                    <td>{{ $order->Order_date }}</td>
                                    <td>
                                        <a href="{{ url('/admin/orderitem?order_id=' . $order->Order_id) }}" class="view-items-btn">View Items</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="text-align: center;">No orders found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Container -->
                <div class="pagination-container">
                    {{-- Previous Button --}}
                    @if ($orders->currentPage() > 1)
                        <a class="page-btn" href="{{ $orders->previousPageUrl() }}">Previous</a>
                    @else
                        <span class="page-btn disabled">Previous</span>
                    @endif

                    {{-- Page Numbers --}}
                    @for ($i = 1; $i <= $orders->lastPage(); $i++)
                        @if ($i == $orders->currentPage())
                            <span class="page-number active">{{ $i }}</span>
                        @else
                            <a class="page-number" href="{{ $orders->url($i) }}">{{ $i }}</a>
                        @endif
                    @endfor

                    {{-- Next Button --}}
                    @if ($orders->currentPage() < $orders->lastPage())
                        <a class="page-btn" href="{{ $orders->nextPageUrl() }}">Next</a>
                    @else
                        <span class="page-btn disabled">Next</span>
                    @endif
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

    <script src="{{ asset('Javascripts/RealTime.js') }}"></script>
</body>
</html>