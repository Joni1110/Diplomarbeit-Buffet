<link rel="stylesheet" href="{{ asset('css/menu-style.css') }}">


<div class="Header">
<div class="LogoHeader">
    <img src="images/LogoDunkel.png">
    <p>SMART BUFFET</p>
</div>

<nav class="navbar">
    <div class="menu"></div>
    <a href="#" class="toggle-button">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </a>


    <div class="navbar-links">
    <ul>
        <li><a href="{{url('home')}}">Home</a></li>
        <li><a href="{{url('bestellungen')}}">Bestellungen</a></li>
        <li><a href="{{url('ueberuns')}}">Über uns</a></li>
        <li><a href="{{url('kontakt')}}">Kontakt</a></li>
    </ul>
    </div>
</nav>





<div class="Icons">
    <img id="LogoSchuleKlein"src="images/Logo_Schule_KleinDunkel.png">
    <img src="images/User-IconDunkel.png">
</div>






    <script>
       const toggleButton = document.getElementsByClassName('toggle-button')[0]
       const navbarLinks = document.getElementsByClassName("navbar-links")[0]


       toggleButton.addEventListener('click', ()=> {navbarLinks.classList.toggle('active')})
    </script>

</div>
