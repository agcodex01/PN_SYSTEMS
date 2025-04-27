<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Login</title>
    
</head>
<body>
    <div class="login-container">
        <img src="{{ asset('images/pnlogo.png') }}" alt="Logo">
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <label for="user_id">Faculty ID / Student ID</label>
            <input type="text" name="user_id" id="user_id"  >
            <label for="password">Password</label>
            <input type="password" name="password" >
            <button type="submit">Login</button>
        
            <a href="{{ route('forgot-password') }}" >Forgot Password?</a>
        </form>


 
        @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <p class="error-message" style="color: red">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <p class="error-message"  style="color: red">{{ session('error') }}</p>
        </div>
    @endif


    </div>

 
    


    
</body>
</html>