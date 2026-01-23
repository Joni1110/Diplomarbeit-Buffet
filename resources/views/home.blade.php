<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SmartBuffet - Home</title>

    <link rel="stylesheet" href="{{ asset('css/home-style.css') }}">

</head>
<body>

<header>
    @include('components/menu')
</header>

<main>
    <h1 id="ger">Montag</h1>

    {{-- Meldungen --}}
    @if(session('error'))
        <div style="color:#fff; background:#aa0022; padding:10px; border-radius:8px; margin:10px 20px;">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div style="color:#fff; background:#1f7a1f; padding:10px; border-radius:8px; margin:10px 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="container">
        @foreach($produkte as $produkt)
                 <div class="Speise">
                    <h1>{{ $produkt->name }}</h1>
                        <img src="{{ $produkt->bild_url }}" alt="{{ $produkt->name }}">
                     <div class="overlay">
                         <i class="fa-solid fa-cart-shopping fa-2xl"></i>

                     </div>
                    <div class="beschreibung">
                        <h3>€ {{ number_format((float)$produkt->preis, 2, ',', '.') }}</h3>
                        <p>{{ $produkt->beschreibung }}</p>

                        {{-- Warenkorb Add --}}


                </div>
            </div>
        @endforeach
    </div>

    <div style="margin: 20px;">
        <a href="{{ route('warenkorb') }}" style="color:#fff; text-decoration:none; font-weight:600;">
            → Zum Warenkorb / Bestellungen
        </a>
    </div>
</main>

<footer>
    @include('components/footer')
</footer>

</body>
</html>
