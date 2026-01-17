<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrieren - SmartBuffet</title>
    <link rel="stylesheet" href="{{ asset('css/login-style.css') }}">
</head>
<body>
<div class="left">
    <div class="logo">
        <img src="{{ asset('images/Logo_Schule.png') }}" width="50%">
    </div>
    <div class="login-box">
        <h2>Registrieren</h2>

        {{-- EINZELNE Fehlermeldung (z.B. Captcha / Bot) --}}
        @if(session('error'))
            <div class="error" style="color:red; margin-bottom:10px;">
                {{ session('error') }}
            </div>
        @endif

        {{-- Validierungsfehler (z.B. gmail, passwort zu kurz, etc.) --}}
        @if ($errors->any())
            <div class="error" style="color:red; margin-bottom:10px;">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div class="success" style="color:green; margin-bottom:10px;">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.user') }}">
            @csrf

            <!-- Honeypot -->
            <input type="text" name="website" style="display:none">

            <div class="input-group">
                <input type="text" name="vorname" placeholder="Vorname" value="{{ old('vorname') }}" required>
            </div>
            <div class="input-group">
                <input type="text" name="nachname" placeholder="Nachname" value="{{ old('nachname') }}" required>
            </div>
            <div class="input-group">
                <input type="email" name="email" placeholder="E-Mail" value="{{ old('email') }}" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Passwort" required>
            </div>
            <div class="input-group">
                <input type="password" name="password_confirmation" placeholder="Passwort bestätigen" required>
            </div>

            <div class="input-group">
                <label>Captcha: Was ist {{ session('captcha_question') }}?</label>
                <input type="text" name="captcha" required>
            </div>

            <button type="submit" class="btn">Registrieren</button>
        </form>

        <div class="register" style="margin-top:10px;">
            Schon ein Konto? <a href="{{ url('/login') }}">Anmelden</a>
        </div>
    </div>
</div>
<div class="right">
    <p>Jetzt registrieren<br>und genießen!</p>
</div>
</body>
</html>
