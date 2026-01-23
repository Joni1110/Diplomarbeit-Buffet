<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/kontakt-style.css') }}">
    <title>Kontakt - SmartBuffet</title>
</head>
<body>
<header>
    @include('components/menu')
</header>
<main>
<h1>Kontakt</h1>

<div class="kontakt-box">

    {{-- Erfolg / Fehler --}}
    @if(session('success'))
        <p style="color: #2ecc71; margin-bottom: 10px;">{{ session('success') }}</p>
    @endif

    @if(session('error'))
        <p style="color: #ff4d4d; margin-bottom: 10px;">{{ session('error') }}</p>
    @endif

    @if ($errors->any())
        <ul style="color:#ff4d4d; margin-bottom: 10px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('kontakt.send') }}" method="POST">
        @csrf

        <label for="name">Name</label>
        <input type="text" id="name" name="name" placeholder="Dein Name" value="{{ old('name') }}" required>

        <label for="email">E-Mail</label>
        <input type="email" id="email" name="email" placeholder="Deine E-Mail-Adresse" value="{{ old('email') }}" required>

        <label for="nachricht">Nachricht</label>
        <textarea id="nachricht" name="nachricht" placeholder="Wie können wir dir helfen?" required>{{ old('nachricht') }}</textarea>

        <button type="submit">Nachricht senden</button>
    </form>
</div>
</main>
<footer>
    @include('components/footer')
</footer>
</body>
</html>
