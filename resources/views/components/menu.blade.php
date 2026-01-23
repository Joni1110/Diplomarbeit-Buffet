<link rel="stylesheet" href="{{ asset('css/menu-style.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="Header">

    <div class="LogoHeader">
        <img src="{{ asset('images/LogoDunkel.png') }}" alt="Logo">
        <p>SMART BUFFET</p>
    </div>

    <nav class="navbar">
        <a href="#" class="toggle-button">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </a>

        <div class="navbar-links">
            <ul>
                <li><a href="{{ url('/home') }}">Home</a></li>
                <li><a href="{{ url('/bestellungen') }}">Bestellungen</a></li>
                <li><a href="{{ url('/ueberuns') }}">Über uns</a></li>
                <li><a href="{{ url('/kontakt') }}">Kontakt</a></li>
            </ul>
        </div>
    </nav>

    <div class="Icons">

        <!-- Light/Dark Toggle (wie gehabt) -->
        <!--<i id="LightBulb" class="fa-solid fa-lightbulb fa-xl"></i>
        <i id="LightBulbHellerModus" class="fa-regular fa-lightbulb fa-xl"></i> -->
        <a href="/warenkorb"> <i onclick="" class="fa-solid fa-cart-shopping fa-xl"></i></a>

        <!-- USER MENU -->
        <div class="user-menu">
            <button class="user-button" id="userToggle">
                <img src="{{ asset('images/User-IconDunkel.png') }}" alt="User">
            </button>

            <div class="user-dropdown" id="userDropdown">
                <div class="user-dropdown-header">
                    <div class="user-name">{{ session('user_name') }}</div>
                    <div class="user-mail">{{ session('user_email') }}</div>
                </div>

                <a href="{{ url('/logout') }}" class="user-dropdown-item logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Abmelden
                </a>
            </div>
        </div>
    </div>
</div>

<script>



    // Burger Menu
    const toggleButton = document.querySelector('.toggle-button');
    const navbarLinks = document.querySelector('.navbar-links');

    toggleButton.addEventListener('click', () => {
        navbarLinks.classList.toggle('active');
    });

    // Light Mode Toggle (unverändert)
    const licht = document.getElementById('LightBulb');
    const lichtHellerModus = document.getElementById('LightBulbHellerModus');

    lichtHellerModus.style.display = 'none';

    licht.addEventListener('click', () => {
        licht.style.display = 'none';
        lichtHellerModus.style.display = 'block';
        document.body.classList.add('LightMode');
    });

    lichtHellerModus.addEventListener('click', () => {
        lichtHellerModus.style.display = 'none';
        licht.style.display = 'block';
        document.body.classList.remove('LightMode');
    });

    // User Dropdown
    const userToggle = document.getElementById('userToggle');
    const userDropdown = document.getElementById('userDropdown');

    userToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        userDropdown.classList.toggle('open');
    });

    document.addEventListener('click', () => {
        userDropdown.classList.remove('open');
    });
</script>
