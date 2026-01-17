<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passwort vergessen - SmartBuffet</title>
    <link rel="stylesheet" href="{{ asset('css/login-style.css') }}">
</head>
<body>
<div class="left">
    <div class="logo">
        <img src="{{ asset('images/Logo_Schule.png') }}" width="50%">
    </div>

    <div class="login-box">
        <h2>Passwort vergessen</h2>
        <form method="POST" action="{{ route('passwort_vergessen') }}">
            @csrf
            <div class="input-group">
                <input type="email" name="email" placeholder="E-Mail eingeben" required>
            </div>
            <button type="submit" class="btn">Link zum Zurücksetzen senden</button>
        </form>
        <div class="register">
            Zurück zum <a href="{{ url('/login') }}">Login</a>
        </div>
    </div>
</div>
<div class="right">
    <p>Wir helfen dir,<br>dein Passwort<br>zurückzusetzen!</p>
</div>
</body>
</html>
