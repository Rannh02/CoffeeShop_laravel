<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Order Items</title>
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
                    <span><strong style="color:green; font-weight:bolder;">Admin: </strong>{{ session('fullname', 'Admin') }}</span>
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
                <a href="{{ route('admin.orderitem') }}" class="nav-item active"><i class="bi bi-basket"></i> OrderItem</a>
                <a href="{{ route('admin.employee') }}" class="nav-item"><i class="bi bi-person-circle"></i> Employee</a>
                <a href="{{ route('admin.archived') }}" class="nav-item"><i class="bi bi-person-x"></i> Employee Archived</a>
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
                <h1 class="page-title">Order Items</h1>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-container">
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>OrderItem_id</th>
                                <th>Order_id</th>
                                <th>Customer Name</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orderItems as $item)
                                <tr>
                                    <td>{{ $item->OrderItem_id }}</td>
                                    <td>{{ $item->Order_id }}</td>
                                    <td>{{ $item->Customer_name }}</td>
                                    <td>{{ $item->Product_name }}</td>
                                    <td>{{ $item->Quantity }}</td>
                                    <td>{{ number_format($item->UnitPrice, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center;">No order items found</td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                <!-- Pagination Controls -->
                <div class="pagination">
                    <ul>
                        @if(!$orderItems->onFirstPage())
                            <li><a href="{{ $orderItems->url(1) }}">First</a></li>
                            <li><a href="{{ $orderItems->previousPageUrl() }}">Previous</a></li>
                        @endif

                        @for($i = 1; $i <= $orderItems->lastPage(); $i++)
                            <li>
                                <a href="{{ $orderItems->url($i) }}" class="{{ $i === $orderItems->currentPage() ? 'active' : '' }}">
                                    {{ $i }}
                                </a>
                            </li>
                        @endfor

                        @if($orderItems->hasMorePages())
                            <li><a href="{{ $orderItems->nextPageUrl() }}">Next</a></li>
                            <li><a href="{{ $orderItems->url($orderItems->lastPage()) }}">Last</a></li>
                        @endif
                    </ul>
                </div>
            </main>
        </div>
    </div>

<script src="{{ asset('Javascripts/RealTime.js') }}"></script>
</body>
</html>