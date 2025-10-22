<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <link rel="stylesheet" href="{{ asset('LoginSystemcss/Login.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
  <img src="../Images/CoffeeShop.png" alt="Coffee Shop" 
       style="position: fixed; width: 100%; height: 100%; z-index: -1; filter: blur(3px); box-shadow: none;">
  
  <div class="container">
    <div class="image-section">
      <img src="../Images/CoffeeCup.jpg" alt="Coffee Cup" 
           style="width: 100%; height: 100%; object-fit: cover;">
    </div>

    <div class="login-section"> 

      <h2>Admin Login</h2>

      <form method="POST">
    <p>Username:</p>
    <input type="text" name="username" placeholder="Admin" required>
    <p>Password:</p>
    <input type="password" name="password" placeholder="Password" required>
    <div class="button-group">
        <button type="submit">Login</button>
    </div>
</form>

<?php if (!empty($message)): ?>
    <p style="color: red; font-weight: bold;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>


      <div class="register-link">
        <a href="RegisterAccount.php">Register</a><br>
        <a href="{{ url()->previous() }}" class="back-btn">Back</a>
      </div>
    </div>
  </div>
</body>
</html>