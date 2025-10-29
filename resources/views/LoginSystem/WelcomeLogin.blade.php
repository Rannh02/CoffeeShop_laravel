<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome</title>
  <link rel="stylesheet" href="{{ asset('LoginSystemcss/Admin.css') }}">
</head>
<body>


  <!-- PAKI TRANSPARENT GD ANG CONTAINER ANI PAREHAS SA REGISTER CASHIER UG ADMIN THANKS -->
  <img src="../Images/CoffeeShop.png" alt="Coffee Shop" 
       style="position: fixed; width: 100%; height: 100%; z-index: -1; filter: blur(3px); box-shadow: none;">
  <div class="container">
    <div class="image-section">
      <img src="../Images/CoffeeCup.jpg" alt="Coffee Cup" 
           style="width: 100%; height: 100%; object-fit: cover;">
    </div>
    <div class="login-section">
      <h1>Welcome to Berde Kopi</h1>
      <h3 style="margin-top:15%;">Login as</h3>
      <div class="button-group">
        <a href="{{ route('login.cashier') }}">
            <button type="button">Cashier</button>
        </a>
            <span>or</span>
        <a href="{{ route('login.admin') }}">
            <button type="button">Admin</button>
        </a>

    
        
      </div>
      <div class="watermark">
          <a>©CoffeePOS 2025<a>
      </div>
    </div>
  </div>
  
</body>
</html>