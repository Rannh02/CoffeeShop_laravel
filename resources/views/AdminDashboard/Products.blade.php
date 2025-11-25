<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin - Products</title>
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/products.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/productdelete.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/productsmodal.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/productupdate.css') }}">
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

            <!-- Search Bar -->
            <div class="search-container">
                <input 
                    type="text" 
                    id="searchInput" 
                    class="search-input" 
                    placeholder="Search for Products."
                    value="{{ request('search') }}"
                >
                <button class="search-btn" onclick="performSearch()">
                    <i class="bi bi-search"></i>
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

                <div class="pagination-container">
                    @if ($page > 1)
                        <a class="page-btn" href="{{ request()->fullUrlWithQuery(['page' => $page - 1]) }}">Previous</a>
                    @else
                        <span class="page-btn disabled">Previous</span>
                    @endif

                    @for ($i = 1; $i <= $totalPages; $i++)
                        @if ($i == $page)
                            <span class="page-number active">{{ $i }}</span>
                        @else
                            <a class="page-number" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                        @endif
                    @endfor

                    @if ($page < $totalPages)
                        <a class="page-btn" href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}">Next</a>
                    @else
                        <span class="page-btn disabled">Next</span>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <!-- Add Product Modal -->
    <div id="ProductModal" class="modal-overlay">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($errors->any())
                <div class="alert alert-danger" style="background: #f8d7da; padding: 10px; border-radius: 5px; color: #721c24;">
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
            <div id="ingredientContainer">
                <div class="ingredient-row" style="margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <select class="ingredient-select" name="ingredient_ids[]" required style="width: 220px; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
                        <option value="">-- Select Ingredient --</option>
                        @foreach($ingredients as $ingredient)
                            <option value="{{ $ingredient->Ingredient_id }}">{{ $ingredient->Ingredient_name }}</option>
                        @endforeach
                    </select>
                    <input 
                        type="number" 
                        name="quantities[]" 
                        class="ingredient-qty" 
                        placeholder="Qty used"
                        min="0.01"
                        step="0.01"
                        required
                        style="display: none; width: 100px; padding: 8px; border-radius: 6px; border: 1px solid #ccc;"
                    >
                </div>
            </div>
            <button type="button" 
                    id="addIngredientRow" 
                    style="
                        margin: 15px 0; 
                        padding: 8px 12px; 
                        background: #10b981; 
                        color: black; 
                        font-size: 13px; 
                        border: none; 
                        border-radius: 10px; 
                        cursor: pointer; 
                        font-weight: 500;
                        transition: all 0.3s ease;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                    "
                    onmouseover="this.style.background='#059669'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(0,0,0,0.15)';"
                    onmouseout="this.style.background='#10b981'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 5px rgba(0,0,0,0.1)';">
                    Add Ingredient
            </button>

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
            <button type="submit" class="UpdateBtn">Update</button>
            <button type="button" id="cancelUpdate" class="CancelBtn">Cancel</button>
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
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') performSearch();
});

// ADD ANOTHER INGREDIENT - FULLY WORKING
document.addEventListener('DOMContentLoaded', function () {
    const ingredientContainer = document.getElementById('ingredientContainer');
    const addButton = document.getElementById('addIngredientRow');
    const ingredients = @json($ingredients);

    function createIngredientRow() {
        const row = document.createElement('div');
        row.className = 'ingredient-row';
        row.style.marginBottom = '15px';
        row.style.display = 'flex';
        row.style.alignItems = 'center';
        row.style.gap = '10px';

        const select = document.createElement('select');
        select.name = 'ingredient_ids[]';
        select.className = 'ingredient-select';
        select.required = true;
        select.style.width = '220px';
        select.style.padding = '8px';
        select.style.borderRadius = '6px';
        select.style.border = '1px solid #ccc';

        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = '-- Select Ingredient --';
        select.appendChild(defaultOption);

        ingredients.forEach(ing => {
            const option = document.createElement('option');
            option.value = ing.Ingredient_id;
            option.textContent = ing.Ingredient_name;
            select.appendChild(option);
        });

        const qtyInput = document.createElement('input');
        qtyInput.type = 'number';
        qtyInput.name = 'quantities[]';
        qtyInput.className = 'ingredient-qty';
        qtyInput.placeholder = 'Qty used';
        qtyInput.min = '0.01';
        qtyInput.step = '0.01';
        qtyInput.required = true;
        qtyInput.style.display = 'none';
        qtyInput.style.width = '100px';
        qtyInput.style.padding = '8px';
        qtyInput.style.borderRadius = '6px';
        qtyInput.style.border = '1px solid #ccc';

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.textContent = 'Remove';
        removeBtn.style.background = '#dc3545';
        removeBtn.style.color = 'white';
        removeBtn.style.border = 'none';
        removeBtn.style.padding = '8px 12px';
        removeBtn.style.borderRadius = '6px';
        removeBtn.style.cursor = 'pointer';
        removeBtn.onclick = () => row.remove();

        row.appendChild(select);
        row.appendChild(qtyInput);
        row.appendChild(removeBtn);

        select.addEventListener('change', function () {
            qtyInput.style.display = this.value ? 'inline-block' : 'none';
            if (!this.value) qtyInput.value = '';
        });

        return row;
    }

    addButton.addEventListener('click', () => {
        ingredientContainer.appendChild(createIngredientRow());
    });

    // Handle existing first row
    document.querySelectorAll('.ingredient-select').forEach(select => {
        select.addEventListener('change', function () {
            const qty = this.nextElementSibling;
            qty.style.display = this.value ? 'inline-block' : 'none';
            if (!this.value) qty.value = '';
        });
    });
});
</script>
</body>
</html>