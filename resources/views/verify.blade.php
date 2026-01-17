<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Mail bestätigen - SmartBuffet</title>
    <link rel="stylesheet" href="{{ asset('css/login-style.css') }}">
</head>
<body>

<div class="left">
    <div class="logo">
        <img src="{{ asset('images/Logo_Schule.png') }}" width="50%">
    </div>

    <div class="login-box">
        <h2>E-Mail bestätigen</h2>

        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        @if(!request('email') || !request('code'))
            <p style="margin-top: 0;">
                Bitte nutze den Link aus deiner E-Mail, um deine Adresse zu bestätigen.
            </p>
        @else
            <p style="margin-top: 0;">
                Klicke auf „Bestätigen“, um deine E-Mail-Adresse zu aktivieren.
            </p>
        @endif

        <form method="POST" action="{{ route('verify.user') }}">
            @csrf

            <div class="input-group">
                <input type="email" name="email" value="{{ request('email') }}" readonly required>
            </div>

            <div class="input-group">
                <input type="text" name="code" value="{{ request('code') }}" readonly required>
            </div>

            <button type="submit" class="btn">Bestätigen</button>
        </form>

        <div class="register">
            Zurück zum <a href="{{ url('/login') }}">Login</a>
        </div>
    </div>
</div>

<div class="right">
    <p>E-Mail<br>Bestätigung<br>abschließen</p>
</div>

</body>
</html>
