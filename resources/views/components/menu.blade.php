<link rel="stylesheet" href="{{ asset('css/menu-style.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


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
    <i id="LightBulb" class="fa-solid fa-lightbulb fa-2xl" style="color: #ffffff;"></i>
    <i  id="LightBulbHellerModus" class="fa-regular fa-lightbulb fa-2xl"></i>
    <img src="images/User-IconDunkel.png">
</div>






    <script>
       const toggleButton = document.getElementsByClassName('toggle-button')[0]
       const navbarLinks = document.getElementsByClassName("navbar-links")[0]


       toggleButton.addEventListener('click', ()=> {navbarLinks.classList.toggle('active')})

       const licht = document.getElementById('LightBulb');
       const lichtHellerModus = document.getElementById('LightBulbHellerModus');


       lichtHellerModus.style.display = 'none';

       licht.addEventListener('click', ()=>{
         licht.style.display = 'none';
         lichtHellerModus.style.display = 'block';
         document.body.classList.add('LightMode');
       })

       lichtHellerModus.addEventListener('click', ()=> {
           lichtHellerModus.style.display = 'none';
           licht.style.display = 'block';
           document.body.classList.remove('LightMode');

       })




    </script>

</div>
