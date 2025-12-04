<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome</title>
  <link rel="stylesheet" href="{{ asset('LoginSystemcss/Admin.css') }}">
  <style>
    /* Ensure images are visible */
    .background-image {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      z-index: -1;
      filter: blur(3px);
    }
    
    .image-section img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }
  </style>
</head>
<body>

  <!-- Background Image -->
  <img src="{{ asset('images/CoffeeShop.png') }}" alt="Coffee Shop" class="background-image">
  
  <div class="container">
    <div class="image-section">
      <img src="{{ asset('images/CoffeeCup.jpg') }}" alt="Coffee Cup">
    </div>
    <div class="login-section">
      <h1>Welcome to Berde Kopi</h1>
      <h3 style="margin-top:15%;">Login as</h3>
      <div class="button-group">
        <a href="{{ route('cashier.login') }}">
            <button type="button">Cashier</button>
        </a>
        <span>or</span>
        <a href="{{ route('login.admin') }}">
            <button type="button">Admin</button>
        </a>
      </div>
      <div class="watermark">
          <span>©CoffeePOS 2025</span>
      </div>
    </div>
  </div>
  
</body>
</html>