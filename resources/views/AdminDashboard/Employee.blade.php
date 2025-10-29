<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Employee</title>
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/employee.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/employeemodal.css') }}">
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
                @if(session('success'))
                    <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Products Header -->
                <div class="products-header">
                    <h1 class="page-title">Employee</h1>
                    <button id="openCategoryModal" class="add-product-btn">
                        <i class="bi bi-plus-circle">   Add Employee</i>
                </button>
                </div>

                <!-- Products Table -->
                <div class="table-container">
                    <div class="table-wrapper">
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Cashier Account</th>
                                    <th>Password</th>
                                    <th>Gender</th>
                                    <th>Contact</th>
                                    <th>Position</th>
                                    <th>Hire Date</th>
                                    <th>Archive</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $employee)
                                    <tr>
                                        <td>{{ $employee->Employee_id }}</td>
                                        <td>{{ $employee->First_name }}</td>
                                        <td>{{ $employee->Last_name }}</td>
                                        <td>{{ $employee->Cashier_Account }}</td>
                                        <td>*******</td>
                                        <td>{{ $employee->Gender }}</td>
                                        <td>{{ $employee->Contact_number }}</td>
                                        <td>{{ $employee->Position }}</td>
                                        <td>{{ $employee->Date_of_Hire }}</td>
                                        <td>
                                            <button class='archive-btn' data-id='{{ $employee->Employee_id }}'>
                                                Archive
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" style="text-align: center;">No active employees found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>

        <!-- Employee Modal -->
        <div id="EmployeeModal" class="modal-overlay" style="display: none;">
            <div class="Employee-Modal">
                <h1>Add Employee</h1>
                <form action="{{ route('admin.employee.store') }}" method="POST">
                    @csrf
                    <p>First Name</p>
                    <input type="text" name="firstname" value="{{ old('firstname') }}" required>

                    <p>Last Name</p>
                    <input type="text" name="lastname" value="{{ old('lastname') }}" required>

                    <p>Cashier Account</p>
                    <input type="text" name="cashierAccount" value="{{ old('cashierAccount') }}" required>

                    <p>Password</p>
                    <input type="password" name="password" required>

                    <p>Gender</p>
                    <select name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                    </select>

                    <p>Contact</p>
                    <input type="text" name="contact" value="{{ old('contact') }}" required>

                    <p>Position</p>
                    <select name="position" required>
                        <option value="">Select Position</option>
                        <option value="Cashier" {{ old('position') == 'Cashier' ? 'selected' : '' }}>Cashier</option>
                        <option value="Manager" {{ old('position') == 'Manager' ? 'selected' : '' }}>Manager</option>
                        <option value="Staff" {{ old('position') == 'Staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                    <br><br>

                    <div class="button-group">
                        <button type="submit" class="AddBtn">Add Employee</button>
                        <button type="button" id="closeEmployeeModal" class="CancelBtn">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('Javascripts/employee.js') }}"></script>
    <script src="{{ asset('Javascripts/RealTime.js') }}"></script>
</body>
</html>