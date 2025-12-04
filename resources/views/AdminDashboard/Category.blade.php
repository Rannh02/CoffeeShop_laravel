<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Category</title>
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/category.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/categoryModal.css') }}">
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
                    <a href="{{ route('admin.orderitem') }}" class="nav-item"><i class="bi bi-basket"></i> OrderItem</a>
                    <a href="{{ route('admin.employee') }}" class="nav-item"><i class="bi bi-person-circle"></i> Employee</a>
                    <a href="{{ route('admin.archived') }}" class="nav-item"><i class="bi bi-person-x"></i> Employee Archived</a>
                    <a href="{{ route('admin.inventory') }}" class="nav-item"><i class="bi bi-cart-check"></i> Inventory</a>
                    <a href="{{ route('admin.ingredients') }}" class="nav-item"><i class="bi bi-check2-square"></i> Ingredients</a>
                    <a href="{{ route('admin.payment') }}" class="nav-item"><i class="bi bi-cash-coin"></i> Payment</a>
                    <a href="{{ route('admin.category') }}" class="nav-item active"><i class="bi bi-tags"></i> Category</a>
                    <a href="{{ route('admin.logout') }}" class="nav-item logout">
                        <i class="bi bi-box-arrow-left"></i> Logout
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="main-content">
                @if(session('error'))
                    <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Button in header -->
                <div class="products-header">
                    <h1 class="page-title">Categories</h1>
                    <button id="openCategoryModal" class="add-product-btn">
                        <i class="bi bi-plus-circle">   Add Category</i>
                    </button>
                </div>

                <!-- Search Bar -->
                <div class="search-container">
                    <input 
                        type="text" 
                        id="searchInput" 
                        class="search-input" 
                        placeholder="Search for Categories."
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
                                <th>Category ID</th>
                                <th>Category Name</th>
                                <th>Update</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>{{ $category->Category_id }}</td>
                                    <td>{{ $category->Category_name }}</td>
                                    <td>
                                        <button class="update-btn" data-category-id="{{ $category->Category_id }}" data-category-name="{{ $category->Category_name }}">Update</button>
                                    </td>
                                    <td>
                                        <button class="delete-btn" data-category-id="{{ $category->Category_id }}" data-category-name="{{ $category->Category_name }}">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center;">No categories found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Container -->
                <div class="pagination-container">
                    {{-- Previous Button --}}
                    @if ($categories->currentPage() > 1)
                        <a class="page-btn" href="{{ $categories->previousPageUrl() }}">Previous</a>
                    @else
                        <span class="page-btn disabled">Previous</span>
                    @endif

                    {{-- Page Numbers --}}
                    @for ($i = 1; $i <= $categories->lastPage(); $i++)
                        @if ($i == $categories->currentPage())
                            <span class="page-number active">{{ $i }}</span>
                        @else
                            <a class="page-number" href="{{ $categories->url($i) }}">{{ $i }}</a>
                        @endif
                    @endfor

                    {{-- Next Button --}}
                    @if ($categories->currentPage() < $categories->lastPage())
                        <a class="page-btn" href="{{ $categories->nextPageUrl() }}">Next</a>
                    @else
                        <span class="page-btn disabled">Next</span>
                    @endif
                </div>
            </main>
        </div>

        <!-- Modal for adding category -->
        <div id="CategoryModal" class="modal-overlay" style="display:none;">
            <form action="{{route('category.store')}}" method="POST" class="modal-content">
                @csrf
                <span class="close-button" id="closeCategoryModal">&times;</span>
                <h2>Add New Category</h2>
                
                <label for="Category_name">Category Name:</label>
                <input type="text" id="Category_name" name="Category_name" required>
                
                <div class="btn-group">
                    <button type="submit" class="submit-btn">Add Category</button>
                    <button type="button" class="cancel-btn" id="cancelCategoryBtn">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Update Category Modal -->
        <div id="UpdateCategoryModal" class="modal-overlay" style="display:none;">
            <form id="updateCategoryForm" method="POST" class="modal-content">
                @csrf
                <span class="close-button" id="closeUpdateCategoryModal">&times;</span>
                <h2>Update Category</h2>
                @method('POST')
                <label for="update_Category_name">Category Name:</label>
                <input type="text" id="update_Category_name" name="Category_name" required>
                <div class="btn-group">
                    <button type="submit" class="submit-btn">Update Category</button>
                    <button type="button" class="cancel-btn" id="cancelUpdateCategoryBtn">Cancel</button>
                </div>
            </form>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="DeleteCategoryModal" class="modal-overlay" style="display:none;">
            <div class="modal-content">
                <h2>Confirm Deletion</h2>
                <p>Are you sure you want to delete <strong id="deleteCategoryName"></strong>?</p>
                <form id="deleteCategoryForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="btn-group">
                        <button type="submit" class="submit-btn">Yes, Delete</button>
                        <button type="button" class="cancel-btn" id="cancelDeleteCategoryBtn">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript files -->
    <script src="{{ asset('Javascripts/RealTime.js') }}"></script>
    <script src="{{ asset('Javascripts/categoryModal.js') }}"></script>
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
        
        // Update & Delete handlers for categories
        document.addEventListener('DOMContentLoaded', function () {
            // Update
            const updateButtons = document.querySelectorAll('.update-btn');
            const updateModal = document.getElementById('UpdateCategoryModal');
            const updateForm = document.getElementById('updateCategoryForm');
            const updateNameInput = document.getElementById('update_Category_name');
            const closeUpdate = document.getElementById('closeUpdateCategoryModal');
            const cancelUpdate = document.getElementById('cancelUpdateCategoryBtn');

            updateButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.getAttribute('data-category-id');
                    const name = this.getAttribute('data-category-name');
                    updateNameInput.value = name;
                    updateForm.action = '/admin/category/' + id + '/update';
                    updateModal.style.display = 'block';
                });
            });

            if (closeUpdate) closeUpdate.addEventListener('click', () => updateModal.style.display = 'none');
            if (cancelUpdate) cancelUpdate.addEventListener('click', () => updateModal.style.display = 'none');

            // Delete
            const deleteButtons = document.querySelectorAll('.delete-btn');
            const deleteModal = document.getElementById('DeleteCategoryModal');
            const deleteForm = document.getElementById('deleteCategoryForm');
            const deleteNameSpan = document.getElementById('deleteCategoryName');
            const cancelDelete = document.getElementById('cancelDeleteCategoryBtn');

            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.getAttribute('data-category-id');
                    const name = this.getAttribute('data-category-name');
                    deleteNameSpan.textContent = name;
                    deleteForm.action = '/admin/category/' + id;
                    deleteModal.style.display = 'block';
                });
            });

            if (cancelDelete) cancelDelete.addEventListener('click', () => deleteModal.style.display = 'none');
        });
    </script>
</body>
</html>