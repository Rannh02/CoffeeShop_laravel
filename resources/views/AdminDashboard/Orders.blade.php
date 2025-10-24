<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Orders</title>
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/Orders.css') }}">
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
                    <a href="{{ route('admin.orders') }}" class="nav-item"><i class="bi bi-bag-check-fill"></i> Orders</a>
                    <a href="{{ route('admin.orderitem') }}" class="nav-item"><i class="bi bi-basket"></i> OrderItem</a>
                    <a href="{{ route('admin.employee') }}" class="nav-item"><i class="bi bi-person-circle"></i> Employee</a>
                    <a href="{{ route('admin.archived') }}" class="nav-item"><i class="bi bi-person-x"></i> Archived</a>
                    <a href="{{ route('admin.inventory') }}" class="nav-item"><i class="bi bi-cart-check"></i> Inventory</a>
                    <a href="{{ route('admin.ingredients') }}" class="nav-item"><i class="bi bi-check2-square"></i> Ingredients</a>
                    <a href="{{ route('admin.suppliers') }}" class="nav-item"><i class="bi bi-box-fill"></i> Supplier</a>
                    <a href="{{ route('admin.payment') }}" class="nav-item"><i class="bi bi-cash-coin"></i> Payment</a>
                    <a href="{{ route('admin.category') }}" class="nav-item"><i class="bi bi-tags"></i> Category</a>
                    <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="nav-item logout" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer; color: inherit; font: inherit; padding: 0.75rem 1rem;">
                            <i class="bi bi-box-arrow-left"></i> Logout
                        </button>
                    </form>
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>{{ $order->Order_id }}</td>
                                    <td>{{ $order->Customer_id }}</td>
                                    <td>{{ $order->Employee_id }}</td>
                                    <td>{{ $order->{'Customers Name'} }}</td>
                                    <td>{{ $order->OrderDate }}</td>
                                    <td>₱{{ number_format($order->TotalAmount, 2) }}</td>
                                    <td>{{ $order->OrderType }}</td>
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

                <div class="pagination">
                    <ul>
                        @if($page > 1)
                            <li><a href="?page=1">First</a></li>
                            <li><a href="?page={{ $page - 1 }}">Previous</a></li>
                        @endif

                        @for($i = 1; $i <= $totalPages; $i++)
                            <li>
                                <a href="?page={{ $i }}" class="{{ $i === $page ? 'active' : '' }}">
                                    {{ $i }}
                                </a>
                            </li>
                        @endfor

                        @if($page < $totalPages)
                            <li><a href="?page={{ $page + 1 }}">Next</a></li>
                            <li><a href="?page={{ $totalPages }}">Last</a></li>
                        @endif
                    </ul>
                </div>
            </main>
        </div>
    </div>
    <script src="{{ asset('Javascripts/RealTime.js') }}"></script>
</body>
</html>