<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Dashboard</title>
    <link rel="stylesheet" href="{{ asset('dashboard/Dashboard.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div id="app">
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
            <span><strong style="color:green; font-weight:bolder;">Admin: </strong></span>
          </div>
        </div>
    </header>

    <div class="main-container">
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="nav-item active"><i class="bi bi-list"></i> Dashboard</a>
                <a href="{{ route('admin.products') }}" class="nav-item"><i class="bi bi-bag"></i> Products</a>
                <a href="{{ route('admin.orders') }}" class="nav-item"><i class="bi bi-bag-check-fill"></i> Orders</a>
                <a href="{{ route('admin.orderitem') }}" class="nav-item"><i class="bi bi-basket"></i> OrderItem</a>
                <a href="{{ route('admin.employee') }}" class="nav-item"><i class="bi bi-person-circle"></i> Employee</a>
                <a href="{{ route('admin.archived') }}" class="nav-item"><i class="bi bi-person-x"></i> Archived</a>
                <a href="{{ route('admin.inventory') }}" class="nav-item"><i class="bi bi-cart-check"></i> Inventory</a>
                <a href="{{ route('admin.ingredients') }}" class="nav-item"><i class="bi bi-check2-square"></i> Ingredients</a>
                <a href="{{ route('admin.supplier') }}" class="nav-item"><i class="bi bi-box-fill"></i> Supplier</a>
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

        <main class="main-content">
            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <div class="stat-number">{{ $totalCustomers }}</div>
                        <div class="stat-label">Customers</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <div class="stat-number">{{ $totalOrders }}</div>
                        <div class="stat-label">Orders</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <div class="stat-number">₱{{ number_format($totalIncome, 2) }}</div>
                        <div class="stat-label">Total Sales</div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="charts-grid">
                <div class="chart-card">
                    <h3>Top 5 Products</h3>
                    <canvas id="topProductsChart"></canvas>
                </div>

                <div class="chart-card">
                    <h3>Weekly Sales</h3>
                    <canvas id="weeklySalesChart"></canvas>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    window.chartData = {
        topProductsLabels: @json($topProducts->pluck('Product_name')),
        topProductsValues: @json($topProducts->pluck('total_sold')),
        weeklySalesLabels: @json(array_keys($chartSales)),
        weeklySalesValues: @json(array_values($chartSales))
    };

    // Real-time clock
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit',
            hour12: true 
        });
        document.getElementById('currentTime').textContent = timeString;
    }
    
    updateTime();
    setInterval(updateTime, 1000);
</script>
<script src="{{ asset('js/JS_Dashboard/dashboardCharts.js') }}"></script>
</body>
</html>