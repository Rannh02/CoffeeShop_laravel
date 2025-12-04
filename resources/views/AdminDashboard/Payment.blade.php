<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Payments</title>
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/payment.css') }}">
    
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
                <a href="{{ route('admin.inventory') }}" class="nav-item"><i class="bi bi-cart-check"></i> Inventory</a>
                <a href="{{ route('admin.ingredients') }}" class="nav-item"><i class="bi bi-check2-square"></i> Ingredients</a>
                <a href="{{ route('admin.payment') }}" class="nav-item active"><i class="bi bi-cash-coin"></i> Payment</a>
                <a href="{{ route('admin.category') }}" class="nav-item"><i class="bi bi-tags"></i> Category</a>
                <a href="{{ route('admin.logout') }}" class="nav-item logout">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="products-header">
                <h1 class="page-title">Payments</h1>
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
                    placeholder="Search for Payments."
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
                            <th>Order Date</th>
                            <th>Total Amount</th>
                            <th>Order Type</th>
                            <th>Payment Method</th>
                            <th>Amount Paid</th>
                            <th>Transaction Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->Order_id }}</td>
                                <td>{{ $payment->order->Customer_id ?? '-' }}</td>
                                <td>{{ $payment->order->Employee_id ?? '-' }}</td>
                                <td>{{ $payment->order->Customer_name ?? '-' }}</td>
                                <td>{{ $payment->order->Order_date ? \Carbon\Carbon::parse($payment->order->Order_date)->format('Y-m-d H:i:s') : '-' }}</td>
                                <td>₱{{ number_format($payment->order->TotalAmount ?? 0, 2) }}</td>
                                <td>{{ $payment->order->Order_Type ?? '-' }}</td>
                                <td>{{ $payment->PaymentMethod ?? '-' }}</td>
                                <td>₱{{ number_format($payment->AmountPaid ?? 0, 2) }}</td>
                                <td>{{ $payment->TransactionReference ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="text-align: center;">No payment records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Container -->
            <div class="pagination-container">
                {{-- Previous Button --}}
                @if ($payments->currentPage() > 1)
                    <a class="page-btn" href="{{ $payments->previousPageUrl() }}">Previous</a>
                @else
                    <span class="page-btn disabled">Previous</span>
                @endif

                {{-- Page Numbers --}}
                @for ($i = 1; $i <= $payments->lastPage(); $i++)
                    @if ($i == $payments->currentPage())
                        <span class="page-number active">{{ $i }}</span>
                    @else
                        <a class="page-number" href="{{ $payments->url($i) }}">{{ $i }}</a>
                    @endif
                @endfor

                {{-- Next Button --}}
                @if ($payments->currentPage() < $payments->lastPage())
                    <a class="page-btn" href="{{ $payments->nextPageUrl() }}">Next</a>
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
</body>
</html>