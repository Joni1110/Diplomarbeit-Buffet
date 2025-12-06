<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{asset('css/home-style.css')}}">
</head>
<body>
@include('components/menu')
<main>
    <i id="LightBulb" class="fa-solid fa-lightbulb fa-2xl" style="color: #ffffff;"></i>
    <h1 id="ger">Montag</h1>

    <div class="container">

        <div class="Speise Montag">
            <h1>Schnitzel mit Pommes</h1>
            <img src="images/schnitzel.jpg">
            <div class="beschreibung">
                <h3>€ 7,50</h3>
                <p>Saftiges Schnitzel mit goldbraunen Pommes und Marmelade</p>
            </div>
        </div>

        <div class="Speise Dienstag">
            <h1>Schnitzel mit Pommes</h1>
            <img src="images/schnitzel.jpg">
            <div class="beschreibung">
                <h3>€ 7,50</h3>
                <p>Saftiges Schnitzel mit goldbraunen Pommes und Marmelade</p>
            </div>
        </div>

        <div class="Speise Mittwoch">
            <h1>Schnitzel mit Pommes</h1>
            <img src="images/schnitzel.jpg">
            <div class="beschreibung">
                <h3>€ 7,50</h3>
                <p>Saftiges Schnitzel mit goldbraunen Pommes und Marmelade</p>
            </div>
        </div>



    </div>






    <h1 id="ger">Dienstag</h1>

    <div class="container">

        <div class="Speise Montag">
            <h1>Montag</h1>
            <img src="images/schnitzel.jpg">
            <div class="beschreibung">
                <h2>Schnitzel mit Pommes</h2>
                <h3>€ 7,50</h3>
            </div>
        </div>

        <div class="Speise Dienstag">
            <h1>Dienstag</h1>
            <img src="images/schnitzel.jpg">
            <div class="beschreibung">
                <h2>Schnitzel mit Pommes</h2>
                <h3>€ 7,50</h3>
            </div>
        </div>

        <div class="Speise Mittwoch">
            <h1>Mittwoch</h1>
            <img src="images/schnitzel.jpg">
            <div class="beschreibung">
                <h2>Schnitzel mit Pommes</h2>
                <h3>€ 7,50</h3>
            </div>
        </div>



    </div>





</main>


<footer>
@include('components/footer')
</footer>
</body>
</html>
