<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartBuffet - Login</title>
    <link rel="stylesheet" href="{{ asset('css/login-style.css') }}">
</head>
<body>
<div class="left">
    <div class="logo">
        <img src="{{ asset('images/Logo_Schule.png') }}" width="50%">
    </div>

    <div class="login-box">
        <h2>Login</h2>

        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('login.user') }}">
            @csrf

            <div class="input-group">
                <input type="email" name="email" placeholder="E-Mail" required>
            </div>

            <div class="input-group">
                <input type="password" name="password" placeholder="Passwort" required>
            </div>

            {{-- Angemeldet bleiben --}}
            <div class="input-group" style="display:flex; align-items:center; gap:10px; justify-content:flex-start;">
                <input type="checkbox" id="remember" name="remember" value="1" style="width:auto;">
                <label for="remember" style="margin:0;">Angemeldet bleiben</label>
            </div>

            <a href="{{ url('/passwort_vergessen') }}" class="forgot">Passwort vergessen?</a>
            <button type="submit" class="btn">Anmelden</button>
        </form>

        <div class="register">
            Noch keinen Account? <a href="{{ url('/register') }}">Registrieren</a>
        </div>
    </div>
</div>

<div class="right">
    <p>Jeden Tag<br>frische<br>Speisen</p>
</div>
</body>
</html>
