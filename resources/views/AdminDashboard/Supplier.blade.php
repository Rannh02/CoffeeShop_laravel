<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin - Products</title>
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/supplierModal.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/supplier.css') }}">
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
                    <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 
                    14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 
                    9.76142 9.23858 12 12 12Z" fill="currentColor"/>
                    <path d="M12 14C7.58172 14 4 17.5817 
                    4 22H20C20 17.5817 16.4183 14 12 14Z" fill="currentColor"/>
                </svg>
                <span>
                    <strong style="color:green; font-weight:bolder;">Admin: </strong>
                </span>
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
        <h1 class="page-title">Suppliers</h1>
        <button id="openAddSupplierModal" class="add-product-btn">
                <i class="bi bi-plus-circle">   Add Category</i>
        </button>
      </div>

      <style>
        .status-message { margin-bottom: 10px; }
        .status-message.error { color: red; }
        .status-message.success { color: green; }
      </style>

      @if(session('status_message'))
        <div class="status-message {{ str_contains(session('status_message'), 'Error') ? 'error' : 'success' }}">
          {{ session('status_message') }}
        </div>
      @endif

      <div class="table-container">
        <table class="products-table">
          <thead>
            <tr>
              <th>Supplier ID</th>
              <th>Supplier Name</th>
              <th>Contact</th>
              <th>Address</th>
              <th>Status</th>
              <th>Update</th>
              <th>Archive</th>
            </tr>
          </thead>
          <tbody>
            @forelse($suppliers as $sup)
              <tr>
                <td>{{ $sup->Supplier_id }}</td>
                <td>{{ $sup->Supplier_name }}</td>
                <td>{{ $sup->Contact_number }}</td>
                <td>{{ $sup->Address }}</td>
                <td>{{ $sup->Status ?? 'N/A' }}</td>
                <td>
                  <button class="update-btn" 
                      data-id="{{ $sup->Supplier_id }}"
                      data-name="{{ $sup->Supplier_name }}" 
                      data-contact="{{ $sup->Contact_number }}" 
                      data-address="{{ $sup->Address }}">Update</button></td>
                <td>
                  @if($sup->Status === 'active')
                    <button class="archive-btn" data-id="{{ $sup->id }}">Archive</button>
                  @else
                    <button class="restore-btn" data-id="{{ $sup->id }}">Restore</button>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="7">No suppliers found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </main>

<!-- Add Supplier Modal -->
<div id="SupplyModal" class="modal-overlay" style="display:none;">
    <form action="{{ route('suppliers.store') }}" method="POST" class="modal-content">
        @csrf
        <h1>Add Supplier</h1>

        <p>Supplier Name</p>
        <input type="text" name="Supplier_name" required>

        <p>Contact</p>
        <input type="text" name="Contact_number" required>

        <p>Address</p>
        <input type="text" name="Address" required>

        <div class="btn-group">
            <button type="submit" class="AddBtn">Add Supplier</button>
            <button type="button" id="closeSupplyModal" class="CancelBtn">Cancel</button>
        </div>
    </form>
</div>

      <!-- Update Modal -->
<div id="EditSupplyModal" class="modal-overlay" style="display:none;">
  <form id="editSupplierForm" method="POST" class="modal-content">
    @csrf
    <input type="hidden" id="editSupplierId" name="Supplier_id">
    
    <h1>Update Supplier</h1>
    
    <p>Supplier Name</p>
    <input type="text" id="editSupplierName" name="Supplier_name" required>
    
    <p>Contact</p>
    <input type="text" id="editContact" name="Contact_number" required>
    
    <p>Address</p>
    <input type="text" id="editAddress" name="Address" required>
    
    <div class="btn-group">
      <button type="submit" class="AddBtn">Update</button>
      <button type="button" id="closeEditSupplierModal" class="CancelBtn">Cancel</button>
    </div>
  </form>
</div>
  </div>
</div>
<script src="{{ asset('Javascripts/RealTime.js') }}"></script>
<script src="{{ asset('Javascripts/supplierModal.js') }}"></script>
<script src="{{ asset('Javascripts/supplierUpdate.js') }}"></script>
<script src="{{ asset('Javascripts/supplierArchive.js') }}"></script>

  </body>
</html>
