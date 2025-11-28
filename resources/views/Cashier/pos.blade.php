<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berde Kopi - {{ $selectedCategory->Category_name ?? 'POS' }}</title>
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/cashier.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/modalQR.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/customerInfo.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard CSS/confirmationOrder.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="pos-container">
        <!-- Header -->
        <header class="header">
            <div class="logo">
                <i class="bi bi-cup-hot-fill"></i>
                <span class="logo-text">Berde Kopi</span>
            </div>
            <div class="header-info">
                <span id="currentTime" class="time">Time</span>
                
                <!-- User Dropdown -->
                <div class="staff-name" id="userDropdownToggle">
                    <i style="font-size:24px;" class="bi bi-person-circle"></i>
                    <span>{{ $staffName }}</span>
                    <i class="bi bi-chevron-down" style="font-size:14px;"></i>
                    
                    <!-- Dropdown Menu -->
                    <div class="user-dropdown" id="userDropdown">
                        <!-- User Info Section -->
                        <div class="dropdown-user-info">
                            <div class="user-avatar">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="user-name">{{ $staffName }}</div>
                            <div class="user-role">Cashier</div>
                        </div>
                        
                        <!-- Menu Items -->
                        <div class="dropdown-menu-items">
                            <a href="#" class="dropdown-item">
                                <i class="bi bi-person"></i>
                                <span>My Profile</span>
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="bi bi-gear"></i>
                                <span>Settings</span>
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="bi bi-clock-history"></i>
                                <span>Activity Log</span>
                            </a>
                            <a href="{{ route('cashier.logout') }}" class="dropdown-item logout">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="main-content">
            <div class="menu-panel">
                <nav class="menu-nav">
                    @foreach($categories ?? [] as $category)
                        @php
                            $slug = strtolower(str_replace(' ', '-', $category->Category_name));
                            $isActive = ($selectedCategory->Category_id ?? null) === $category->Category_id;
                        @endphp
                        <button class="nav-item {{ $isActive ? 'active' : '' }}" 
                                onclick="window.location.href='{{ route('cashier.pos', ['category' => $slug]) }}'">
                            {{ $category->Category_name }}  
                        </button>
                    @endforeach
                </nav>

                <!-- Product Grid Fetched from DB -->
                    <div class="product-grid">
                        @if(count($products) > 0)
                            @foreach($products as $prod)
                                @php
                                    $isOutOfStock = $prod->StockQuantity <= 0;
                                @endphp

                                <div class="product-card {{ $isOutOfStock ? 'out-of-stock' : '' }}"
                                    data-name="{{ $prod->Product_name }}"
                                    data-price="{{ $prod->Price }}"
                                    data-stock="{{ $prod->StockQuantity }}"
                                    @if($isOutOfStock) style="pointer-events:none; opacity:0.5;" @endif>

                                    <div class="product-image">
                                        @if(!empty($prod->Image_url))
                                            <img src="{{ asset($prod->Image_url) }}" alt="{{ $prod->Product_name }}">
                                        @else
                                            <img src="{{ asset('ProductImages/default.jpg') }}" alt="No Image">
                                        @endif
                                    </div>

                                    <div class="product-info">
                                        <h3 class="product-name">{{ $prod->Product_name }}</h3>
                                        <span class="product-price">₱{{ number_format($prod->Price, 2) }}</span>
                                    </div>

                                </div>

                            @endforeach
                        @else
                            <p>No products available in this category.</p>
                        @endif
                    </div>

            </div>

            <div class="order-panel">
                <h2>Current Order:</h2>
                <div id="orderContainer">
                    <div id="orderList"></div>
                </div>
                <div class="order-summary">
                    <label for="customerName"><strong>Customer Name:</strong></label>
                    <input type="text" id="customerName" placeholder="Customer name">
                    
                    <div class="order-type">
                        <p><strong>Order Type:</strong></p>
                        <button class="order-type-btn" data-type="Dine In">🍽️ Dine In</button>
                        <button class="order-type-btn" data-type="Takeout">🥡 Take Out</button>
                        <button id="clearOrder" class="clear-btn">Clear Order</button>
                    </div>
                    
                    <p>Total Amount: <span id="totalPrice">₱0</span></p>
                    <p>Total Change: <span id="changePrice">₱0</span></p>
                    <input type="number" id="amountPaid" min="0" step="any" placeholder="Amount Paid">
                    <input id="cardNumber" type="text" placeholder="Card Number" style="display:none; font-size:15px; width:100%;">
                    <input id="referenceNumber" type="text" placeholder="Reference Number" style="display:none; font-size:15px; width:100%;">
                    
                    <div style="margin-top: 15px; padding: 10px; background: #f9f9f9; border-radius: 4px;">
                        <p><strong>Discounts (12%):</strong></p>
                        <div style="display: flex; gap: 15px; margin-top: 8px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" id="pwdCheckbox" style="width: 18px; height: 18px;">
                                <span>PWD</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" id="seniorCheckbox" style="width: 18px; height: 18px;">
                                <span>Senior Citizen</span>
                            </label>
                        </div>
                        <p style="margin-top: 8px; font-size: 12px; color: #666;">Discount: <span id="discountAmount">₱0.00</span></p>
                        <p style="margin: 5px 0 0 0; font-weight: bold; font-size: 14px;">After Discount: <span id="finalTotalPrice">₱0.00</span></p>
                    </div>
                </div>

                <div class="payment-buttons">
                    <button class="payment-btn card"><i class="bi bi-credit-card-2-back"></i> Card</button>
                    <button class="payment-btn gcash"><i class="bi bi-qr-code"></i> E-Wallet</button>
                </div>
                <button id="placeOrderBtn" class="btn btn-primary">Place Order</button>
            </div>
        </div>
    </div>

    <!-- QR Modal -->
    <div id="ewalletModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <button id="closeEwalletModal" class="close-btn">&times;</button>
            <h2>E-Wallet Payment</h2>
            <p>Please scan the QR code with your preferred wallet app.</p>
            <img src="{{ asset('Images/QRcode.png') }}" alt="E-Wallet QR Code" 
                 style="width: 1000px; height: 500px; object-fit:cover;">
        </div>
    </div>

    <!-- Place Order Confirmation Modal -->
    <div id="placeOrderModal" class="modal-overlay" style="display:none;">
        <div class="modal-content">
            <h2>Order Placed!</h2>
            <p>Your order has been successfully placed.</p>
            <div id="receipt" style="display:none; margin-top: 20px; font-family: monospace; white-space: pre-wrap;"></div>
            <div class="modal-buttons">
                <button id="printReceipt" class="btn btn-primary">Print Receipt</button>
                <button id="continueOrder" class="btn btn-secondary">Continue</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('Javascripts/RealTime.js') }}"></script>
    <script src="{{ asset('Javascripts/orderSystem.js') }}"></script>
    <script src="{{ asset('Javascripts/QRlogic.js') }}"></script>
    <script src="{{ asset('Javascripts/OrderTypeButton.js') }}"></script>
    <script src="{{ asset('Javascripts/Inputs.js') }}"></script>
    <script src="{{ asset('Javascripts/placeordermodal.js') }}"></script>
    <script src="{{ asset('Javascripts/orderingcoffee.js') }}"></script>
    <!-- <script src="{{ asset('Javascripts/PaymentSummary.js') }}"></script> -->
    
    
    <!-- User Dropdown Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownToggle = document.getElementById('userDropdownToggle');
        const dropdown = document.getElementById('userDropdown');
        
        // Toggle dropdown on click
        dropdownToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownToggle.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
        
        // Prevent dropdown from closing when clicking inside it
        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
    </script>
</body>
</html>