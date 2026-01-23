<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/warenkorb-style.css') }}">

</head>
@include('components/menu')
<body>
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
                    <div class="einzelpreis">{{ number_format($item->summe, 2, ',', '.') }}</div>

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

<footer>
    @include('components/footer')
</footer>
</body>
</html>
