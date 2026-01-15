<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('css/kontakt-style.css')}}">
    <title>Document</title>
</head>
<body>
<header>
    @include('components/menu')
</header>
<h1>Kontakt</h1>
<div class="kontakt-box">
    <form action="#" method="POST">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" placeholder="Dein Name" required>
        <label for="email">E-Mail</label>
        <input type="email" id="email" name="email" placeholder="Deine E-Mail-Adresse" required>
        <label for="nachricht">Nachricht</label>
        <textarea id="nachricht" name="nachricht" placeholder="Wie können wir dir helfen?" required>
        </textarea>
        <button type="submit">Nachricht senden</button>
    </form>
</div>

<footer>
    @include('components/footer')
</footer>
</body>
</html>
