<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('css/bestellungen-style.css')}}">
    <title>Document</title>
</head>
<body>
<header>
    @include('components/menu')
</header>
<h1>Bisherige Bestellungen</h1>
<div class="bestellungen">
    <div class="karte">
        <h2>Schnitzel mit Pommes</h2>
        <p class="datum">Bestellt am: 13.01.2026</p>
        <p class="preis">Gesamtpreis: € 7,50</p>
    </div> <div class="karte">
        <h2>Schnitzel mit Pommes</h2>
        <p class="datum">Bestellt am: 14.01.2026</p>
        <p class="preis">Gesamtpreis: € 7,50</p>
    </div> <div class="karte">
        <h2>Schnitzel mit Pommes</h2>
        <p class="datum">Bestellt am: 15.01.2026</p>
        <p class="preis">Gesamtpreis: € 7,50</p>
    </div>
</div>
<footer>
    @include('components/footer')
</footer>
</body>
</html>
