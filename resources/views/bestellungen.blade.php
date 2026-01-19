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

<h1>Warenkorb</h1>

@if(session('error'))
    <div class="msg error">{{ session('error') }}</div>
@endif

@if(session('success'))
    <div class="msg success">{{ session('success') }}</div>
@endif

<div class="cart-box">
    @if(empty($cartItems))
        <p class="cart-empty">Dein Warenkorb ist leer.</p>
        <a class="backlink" href="{{ route('home') }}">← Zurück zu den Produkten</a>
    @else
        @foreach($cartItems as $item)
            <div class="cart-item">
                <div class="cart-left">
                    <div class="cart-name">{{ $item->name }}</div>
                    <div class="cart-price">€ {{ number_format($item->preis, 2, ',', '.') }} / Stück</div>
                </div>

                <div class="cart-right">
                    <form method="POST" action="{{ route('cart.update') }}" class="cart-update">
                        @csrf
                        <input type="hidden" name="produkt_id" value="{{ $item->id }}">
                        <input class="cart-qty" type="number" name="menge" min="0" max="20" value="{{ $item->menge }}">
                        <button class="btn-small" type="submit">Update</button>
                    </form>

                    <div class="cart-sum">
                        € {{ number_format($item->summe, 2, ',', '.') }}
                    </div>
                </div>
            </div>
        @endforeach

        <div class="cart-total">
            <span>Gesamt:</span>
            <span>€ {{ number_format($cartTotal, 2, ',', '.') }}</span>
        </div>

        <div class="cart-actions">
            <form method="POST" action="{{ route('cart.clear') }}">
                @csrf
                <button class="btn-outline" type="submit">Warenkorb leeren</button>
            </form>

            <form method="POST" action="{{ route('checkout') }}">
                @csrf
                <button class="btn-primary" type="submit">Jetzt bestellen</button>
            </form>
        </div>
    @endif
</div>

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

<footer>
    @include('components/footer')
</footer>
</body>
</html>
