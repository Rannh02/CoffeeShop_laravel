<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Cashier Login</title>
  <link rel="stylesheet" href="{{ asset('LoginSystemcss/Cashier.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
    <img src="{{ asset('images/CoffeeShop.png') }}" alt="Coffee Shop" 
      style="position: fixed; width: 100%; height: 100%; z-index: -1; filter: blur(3px); box-shadow: none;">

  <div class="container">
    <div class="image-section">
      <img src="{{ asset('images/CoffeeCup.jpg') }}" alt="Coffee Cup" 
           style="width: 100%; height: 100%; object-fit: cover;">
    </div>
      <div class="login-section">
        <a href="WelcomeLogin.php"></a>

          <h1>Cashier Login</h1>
                <!--Auto fill error-->
          {{-- legacy PHP message removed; use Blade errors instead --}}
          @if($errors->any())
            <div class="error-messages" style="color: #fdd; margin-bottom:10px;">
              @foreach($errors->all() as $err)
                <div>{{ $err }}</div>
              @endforeach
            </div>
          @endif
          <!-- Until here -->
            <!-- Form -->
           <form method="POST" action="{{ route('cashier.login') }}">
                @csrf
        <p>Username:</p>
          <input type="text" name="username" placeholder="Cashier Account" value="{{ old('username') }}" required>
              <p>Password:</p>
                  <input type="password" name="password" placeholder="Password" required>
                    <div class="login">
                      <button type="submit" class="login-btn">Login</button>
                    </div>
              </form>  
                              
                
          <!-- back to the welcome page-->
              <a href="{{ url()->previous() }}" class="back-btn">Back</a>
              @if (session('error'))
                  <p style="color:white;">{{ session('error') }}</p>
                @endif
    </div>
  </div>
</body>
</html>