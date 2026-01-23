<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/bestellungen-style.css') }}">

    <title>Bestellungen - SmartBuffet</title>
</head>
<body>
<header>
    @include('components/menu')
</header>
<main>

@if(session('error'))
    <div class="msg error">{{ session('error') }}</div>
@endif

@if(session('success'))
    <div class="msg success">{{ session('success') }}</div>
@endif



<h1 style="margin-top:40px;">Bisherige Bestellungen</h1>

<div class="bestellungen">
    @if($bestellungen->isEmpty())
        <p style="text-align:center; color:#bbb;">Noch keine Bestellungen vorhanden.</p>
    @else
        @foreach($bestellungen as $b)
            <div class="karte">
                <h2>Bestellung #{{ $b->id }}</h2>
                <p class="datum">Bestellt am: {{ \Carbon\Carbon::parse($b->bestellt_am)->format('d.m.Y H:i') }}</p>
                <p class="preis">Gesamtpreis: € {{ number_format((float)$b->gesamtpreis, 2, ',', '.') }}</p>

                @php
                    $pos = $positionenByBestellung[$b->id] ?? [];
                @endphp

                @if(!empty($pos))
                    <div class="positions">
                        @foreach($pos as $p)
                            <div class="pos-row">
                                <span class="pos-name">{{ $p->produkt_name }}</span>
                                <span class="pos-qty">x{{ (int)$p->menge }}</span>
                                <span class="pos-price">€ {{ number_format((float)$p->einzelpreis, 2, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</div>
</main>

<footer>
    @include('components/footer')
</footer>
</body>
</html>
