<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berde Kopi - Coffee</title>

    {{-- CSS Files --}}
    <link rel="stylesheet" href="{{ asset('css/CashierSystem.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modalQR.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customerInfo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ConfirmationOrder.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
                <span class="time">Time</span>
                <span class="staff-name">
                    <i style="font-size:20px;" class="bi bi-person-circle"></i> {{ $staffName ?? 'Cashier' }}
                </span>
                <a href="{{ route('logout') }}" class="checkout-btn">Logout</a>
            </div>
        </header>

        <div class="main-content">
            <div class="menu-panel">
                <nav class="menu-nav">
                    <button class="nav-item" onclick="window.location.href='{{ route('cashier.coffee') }}'">Coffee</button>
                    <button class="nav-item" onclick="window.location.href='{{ route('cashier.tea') }}'">Tea</button>
                    <button class="nav-item" onclick="window.location.href='{{ route('cashier.cold') }}'">Cold Drinks</button>
                    <button class="nav-item" onclick="window.location.href='{{ route('cashier.pastries') }}'">Pastries</button>
                </nav>

                <!-- ✅ Product Grid -->
                <div class="product-grid">
                    @if(count($products) > 0)
                        @foreach($products as $prod)
                            @php
                                $isOutOfStock = $prod->QuantityInStock <= 0;
                            @endphp

                            <div class="product-card {{ $isOutOfStock ? 'out-of-stock' : '' }}"
                                data-name="{{ $prod->Product_name }}"
                                data-price="{{ $prod->Price }}"
                                data-stock="{{ $prod->QuantityInStock }}"
                                @if($isOutOfStock) style="pointer-events:none; opacity:0.5;" @endif>
                                <div class="product-image">
                                    @if(!empty($prod->Image))
                                        <img src="{{ asset($prod->Image) }}" alt="{{ $prod->Product_name }}">
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
                        <p>No coffee products available.</p>
                    @endif
                </div>
            </div>

            <!-- ✅ Order Panel -->
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
                        <button class="order-type-btn" data-type="Take Out">🥡 Take Out</button>
                        <button id="clearOrder" class="clear-btn">Clear Order</button>
                    </div>

                    <p>Total Amount: <span id="totalPrice">₱0</span></p>
                    <p>Total Change: <span id="changePrice">₱0</span></p>
                    <input type="number" id="amountPaid" min="0" step="any" placeholder="Amount Paid">
                    <input id="cardNumber" type="text" placeholder="Card Number" style="display:none; font-size:15px; width:100%;">
                    <input id="referenceNumber" type="text" placeholder="Reference Number" style="display:none; font-size:15px; width:100%;">
                </div>

                <div class="payment-buttons">
                    <button class="payment-btn card"><i class="bi bi-credit-card-2-back"></i> Card</button>
                    <button class="payment-btn gcash"><i class="bi bi-qr-code"></i> E-Wallet</button>
                </div>
                <button id="placeOrderBtn" class="btn btn-primary">Place Order</button>
            </div>
        </div>
    </div>

    <!-- ✅ E-Wallet Modal -->
    <div id="ewalletModal" class="modal-overlay" style="display:none;">
        <div class="modal-content">
            <button id="closeEwalletModal" class="close-btn">&times;</button>
            <h2>E-Wallet Payment</h2>
            <p>Please scan the QR code with your preferred wallet app.</p>
            <img src="{{ asset('CashQRCode/GCASHSHOP.jpg') }}" alt="E-Wallet QR Code"
                 style="width:400px; height:450px; object-fit:cover;">
        </div>
    </div>

    <!-- ✅ Order Confirmation Modal -->
    <div id="placeOrderModal" class="modal-overlay" style="display:none;">
        <div class="modal-content">
            <h2>Order Placed!</h2>
            <p>Your order has been successfully placed.</p>
            <div id="receipt" style="display:none; margin-top:20px; font-family:monospace; white-space:pre-wrap;"></div>
            <div class="modal-buttons">
                <button id="printReceipt" class="btn btn-primary">Print Receipt</button>
                <button id="continueOrder" class="btn btn-secondary">Continue</button>
            </div>
        </div>
    </div>

    {{-- JS Files --}}
    <script src="{{ asset('javascripts/orderingcoffeeJS.js') }}"></script>
    <script src="{{ asset('javascripts/Realtime.js') }}"></script>
    <script src="{{ asset('javascripts/PaymentSummary.js') }}"></script>
    <script src="{{ asset('javascripts/QRlogic.js') }}"></script>
    <script src="{{ asset('javascripts/OrderTypeButton.js') }}"></script>
    <script src="{{ asset('javascripts/Inputs.js') }}"></script>
    <script src="{{ asset('javascripts/PlaceOrderModal.js') }}"></script>
</body>
</html>
