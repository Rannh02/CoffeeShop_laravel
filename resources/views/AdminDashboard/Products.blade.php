<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin - Products</title>
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/products.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/productsmodal.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/productupdate.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
<div id="app">
    <!-- Header -->
    <header class="header">
        <div class="header-left">
            <div class="logo"><span>Berde Kopi</span></div>
        </div>
        <div class="header-right">
            <div class="admin-profile">
                <span class="time" id="currentTime">Time</span>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 
                    14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 
                    9.76142 9.23858 12 12 12Z" fill="currentColor"/>
                    <path d="M12 14C7.58172 14 4 17.5817 
                    4 22H20C20 17.5817 16.4183 14 12 14Z" fill="currentColor"/>
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
                <a href="{{ route('products.index') }}" class="nav-item active"><i class="bi bi-bag"></i> Products</a>
                <a href="{{ route('admin.orders') }}" class="nav-item"><i class="bi bi-bag-check-fill"></i> Orders</a>
                <a href="{{ route('admin.orderitem') }}" class="nav-item"><i class="bi bi-basket"></i> OrderItem</a>
                <a href="{{ route('admin.employee') }}" class="nav-item"><i class="bi bi-person-circle"></i> Employee</a>
                <a href="{{ route('admin.archived') }}" class="nav-item"><i class="bi bi-person-x"></i> Employee Archived</a>
                <a href="{{ route('admin.inventory') }}" class="nav-item"><i class="bi bi-cart-check"></i> Inventory</a>
                <a href="{{ route('admin.ingredients') }}" class="nav-item"><i class="bi bi-check2-square"></i> Ingredients</a>
                <a href="{{ route('suppliers.index') }}" class="nav-item"><i class="bi bi-box-fill"></i> Supplier</a>
                <a href="{{ route('admin.payment') }}" class="nav-item"><i class="bi bi-cash-coin"></i> Payment</a>
                <a href="{{ route('admin.category') }}" class="nav-item"><i class="bi bi-tags"></i> Category</a>
                <a href="{{ route('admin.logout') }}" class="nav-item logout"><i class="bi bi-box-arrow-left"></i> Logout</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="products-header">
                <h1 class="page-title">Products</h1>
                <button id="openCategoryModal" class="add-product-btn">
                    <i class="bi bi-plus-circle"></i> Add Product
                </button>
            </div>

            <!-- Products Table -->
            <div class="table-container">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Category ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Image</th>
                            <th>Update</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($products) > 0)
                            @foreach($products as $prod)
                                <tr>
                                    <td>{{ $prod->Product_id }}</td>
                                    <td>{{ $prod->Category_id }}</td>
                                    <td>{{ $prod->Product_name }}</td>
                                    <td>₱{{ number_format($prod->Price, 2) }}</td>
                                    <td>
                                        @if(!empty($prod->Image_url))
                                            <img src="{{ asset($prod->Image_url) }}" alt="Product Image" width="60">
                                        @else
                                            No Image
                                        @endif
                                    </td>
                                    <td>
                                        <button class="update-btn" data-product-id="{{ $prod->Product_id }}">Update</button>
                                    </td>
                                    <td>
                                        <button class="delete-btn" data-id="{{ $prod->Product_id }}" data-name="{{ $prod->Product_name }}">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="10">No products found.</td></tr>
                        @endif
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($totalPages > 1)
                    <div class="pagination">
                        @if($page > 1)
                            <a href="?page={{ $page - 1 }}" class="page-btn">Previous</a>
                        @endif

                        @for($i = 1; $i <= $totalPages; $i++)
                            <a href="?page={{ $i }}" class="page-btn {{ $i == $page ? 'active' : '' }}">{{ $i }}</a>
                        @endfor

                        @if($page < $totalPages)
                            <a href="?page={{ $page + 1 }}" class="page-btn">Next</a>
                        @endif
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- Add Product Modal -->
    <div id="ProductModal" class="modal-overlay">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($errors->any())
                <div class="alert alert-danger"style="background: #f8d7da; padding: 10px; border-radius: 5px; color: #721c24;">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <h1>Add Product</h1>
            <p>Product Name</p>
            <input type="text" name="Product_name" value="{{ old('Product_name') }}" required>

            <p>Category</p>
            <select name="Category_id" required>
                <option value="">-- Select Category --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->Category_id }}">{{ $cat->Category_name }}</option>
                @endforeach
            </select>

            <label>Ingredients</label>
                <div id="ingredientList">
                    @foreach($ingredients as $ingredient)
                        <div style="margin-bottom: 8px;">
                            <label>
                                <input type="checkbox" name="ingredient_ids[]" value="{{ $ingredient->Ingredient_id }}" class="ingredient-checkbox">
                                {{ $ingredient->Ingredient_name }}
                            </label>
                            <input 
                                type="number" 
                                name="quantities[{{ $ingredient->Ingredient_id }}]" 
                                class="ingredient-qty" 
                                placeholder="Qty used"
                                min="0.01"
                                step="0.01"
                                disabled
                                required
                            >
                        </div>
                    @endforeach
                </div>

            <p>Price</p>
            <input type="number" step="0.01" name="Price" required>

            <label><br>Image:</label>
            <input type="file" name="image">

            <div class="btn-group">
                <button type="submit" class="AddBtn">Add Product</button>
                <button type="button" id="closeProductModal" class="CancelBtn">Cancel</button>
            </div>
        </form>
    </div>

    <!-- Update Product Modal -->
    <div id="UpdateProductModal" class="modal-overlay">
        <form id="updateForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <input type="hidden" name="Product_id" id="updateProductId">

            <h1>Update Product</h1>
            <p>Product name</p>
            <input type="text" name="Product_name" id="updateProductName" required>

            <p>Price</p>
            <input type="number" name="Price" id="updateProductPrice" required>

            <p>Category</p>
            <select name="Category_id" id="updateProductCategory">
                @foreach ($categories as $category)
                    <option value="{{ $category->Category_id }}">{{ $category->Category_name }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal-overlay" style="display:none;">
        <div class="modal-content">
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to delete <strong id="productName"></strong>?</p>
            <form id="deleteForm" action="" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="Product_id" id="deleteProductId">

                <div class="btn-group">
                    <button type="submit" class="AddBtn">Yes, Delete</button>
                    <button type="button" id="cancelDelete" class="CancelBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('Javascripts/RealTime.js') }}"></script>
<script src="{{ asset('Javascripts/productmodal.js') }}"></script>
<script src="{{ asset('Javascripts/productupdate.js') }}"></script>
<script src="{{ asset('Javascripts/productdelete.js') }}"></script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.ingredient-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const qtyInput = this.closest('div').querySelector('.ingredient-qty');
            qtyInput.disabled = !this.checked;
            if (!this.checked) qtyInput.value = ''; // clear if unchecked
        });
    });
});
</script>

<!-- <style>
#ingredientQuantities { margin-top: 10px; }
.ingredient-row { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
</style> -->

</body>
</html>
