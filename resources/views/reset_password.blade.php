<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passwort zurücksetzen - SmartBuffet</title>
    <link rel="stylesheet" href="{{ asset('css/login-style.css') }}">
</head>
<body>

<div class="left">
    <div class="logo">
        <img src="{{ asset('images/Logo_Schule.png') }}" width="50%">
    </div>

    <div class="login-box">
        <h2>Neues Passwort setzen</h2>

        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('password.reset') }}">
            @csrf

            <input type="hidden" name="email" value="{{ request('email') }}">
            <input type="hidden" name="code" value="{{ request('code') }}">

            <div class="input-group">
                <input type="password" name="password" placeholder="Neues Passwort" required>
            </div>

            <div class="input-group">
                <input type="password" name="password_confirmation" placeholder="Passwort bestätigen" required>
            </div>

            <button type="submit" class="btn">Passwort speichern</button>
        </form>
    </div>
</div>

<div class="right">
    <p>Passwort<br>neu<br>setzen</p>
</div>

</body>
</html>
