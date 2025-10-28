<!-- resources/views/AdminDashboard/Archived.blade.php -->

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="/vite.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin - Archived Employees</title>
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/Archived.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/EmployeeModal.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite('resources/css/app.css') <!-- If using Vite -->
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
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
              <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="currentColor"/>
              <path d="M12 14C7.58172 14 4 17.5817 4 22H20C20 17.5817 16.4183 14 12 14Z" fill="currentColor"/>
            </svg>
            <span><strong style="color:green; font-weight:bolder;">Admin: </strong>{{ $fullname }}</span>
          </div>
        </div>
      </header>

      <!-- Sidebar -->
      <div class="main-container">
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
          <div class="product-header">
            <h1 class="page-title">Archived Employees</h1>
            <div class="table-container">
              <div class="table-wrapper">
                <table class="products-table">
                  <thead>
                    <tr>
                      <th>Employee_id</th>
                      <th>First_name</th>
                      <th>Last_name</th>
                      <th>Cashier_Account</th>
                      <th>Gender</th>
                      <th>Contact_number</th>
                      <th>Position</th>
                      <th>Date_of_Hire</th>
                      <th>Restore</th>
                    </tr>
                  </thead>
                  <tbody>
                    @if ($employees->count() > 0)
                      @foreach ($employees as $employee)
                        <tr>
                          <td>{{ $employee->Employee_id }}</td>
                          <td>{{ $employee->First_name }}</td>
                          <td>{{ $employee->Last_name }}</td>
                          <td>{{ $employee->Cashier_Account }}</td>
                          <td>{{ $employee->Gender }}</td>
                          <td>{{ $employee->Contact_number }}</td>
                          <td>{{ $employee->Position }}</td>
                          <td>{{ $employee->Date_of_Hire }}</td>
                          <td><button class="restore-btn" data-id="{{ $employee->Employee_id }}">Restore</button></td>
                        </tr>
                      @endforeach
                    @else
                      <tr><td colspan="9">No archived employees found.</td></tr>
                    @endif
                  </tbody>
                </table>
              </div>

              <!-- Pagination -->
              {{ $employees->links() }}
            </div>
          </div>
        </main>
      </div>
    </div>
    <script src="{{ asset('JS_Dashboard/EmployeeArchive.js') }}"></script>
    <script type="module" src="{{ asset('JS_Dashboard/DashboardsTime.js') }}"></script>
  </body>
</html>